<?php

namespace App\Http\Controllers;

use App\Models\BhuBharathi;
use App\Models\Module;
use App\Models\User;
use App\Models\Pahani;
use App\Models\Mandal;
use App\Models\Village;
use App\Models\PahaniDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    /**
     * User Reports Page
     * Shows user's upload summary with mandal, village, and document statistics
     */
    public function userReport()
    {
        $user = Auth::user();

        // Get user's assigned upload mandals from UserDocumentPermission
        $uploadMandalIds = $user->documentPermission?->upload_mandal_ids ?? [];

        // Total mandals assigned to user
        $totalMandalsAssigned = count($uploadMandalIds);

        // Get mandals data
        $assignedMandals = Mandal::whereIn('id', $uploadMandalIds)
            ->where('is_active', true)
            ->get();

        // Total uploads per mandal
        $mandalsWithUploads = Pahani::whereIn('mandal_id', $uploadMandalIds)
            ->select('mandal_id', DB::raw('COUNT(*) as upload_count'))
            ->groupBy('mandal_id')
            ->get()
            ->keyBy('mandal_id');

        $totalMandalsUploaded = $mandalsWithUploads->count();

        // Total document types in system (fixed, e.g. 68) — moved outside loop, no need to requery each time
        $documentTypesPerVillage = PahaniDocument::where('is_active', true)->count();

        // Build mandal summary with village and document details
        $mandalSummary = [];
        $totalUploads = 0;

        foreach ($assignedMandals as $mandal) {
            // Uploads for this mandal
            $uploadCount = $mandalsWithUploads->get($mandal->id)?->upload_count ?? 0;
            $totalUploads += $uploadCount;

            // Villages in this mandal
            $totalVillagesInMandal = Village::where('mandal_id', $mandal->id)
                ->where('is_active', true)
                ->count();

            // Villages with uploads in this mandal
            $uploadedVillagesInMandal = Pahani::where('mandal_id', $mandal->id)
                ->distinct('village_id')
                ->count('village_id');

            // Total documents expected for this mandal = villages * fixed doc types
            $totalDocumentsInMandal = $totalVillagesInMandal * $documentTypesPerVillage;

            // Documents actually uploaded for this mandal
            $documentsUploadedInMandal = Pahani::where('mandal_id', $mandal->id)
                ->where('physical_document', 'yes')
                ->count();

            $pendingDocuments = $totalDocumentsInMandal - $documentsUploadedInMandal;

            // Completion percentage
            $completionPercentage = $totalDocumentsInMandal > 0
                ? round(($documentsUploadedInMandal / $totalDocumentsInMandal) * 100, 2)
                : 0;

            // Status label instead of % of system
            $status = match (true) {
                $completionPercentage >= 100 => 'Completed',
                $completionPercentage > 0    => 'In Progress',
                default                      => 'Not Started',
            };

            $mandalSummary[] = [
                'id' => $mandal->id,
                'name' => $mandal->name,
                'assigned' => true,
                'total_villages' => $totalVillagesInMandal,
                'uploaded_villages' => $uploadedVillagesInMandal,
                'total_documents' => $totalDocumentsInMandal,
                'uploaded_documents' => $documentsUploadedInMandal,
                'pending_documents' => $pendingDocuments,
                'completion_percentage' => $completionPercentage,
                'status' => $status,
                'upload_count' => $uploadCount, // kept, used later for chart data
            ];
        }

        // Get villages data for uploaded mandals
        $uploadedMandalIds = $mandalsWithUploads->pluck('mandal_id')->toArray();
        $totalVillagesAssigned = Village::whereIn('mandal_id', $uploadMandalIds)
            ->where('is_active', true)
            ->count();

        $villageUploads = Pahani::whereIn('mandal_id', $uploadMandalIds)
            ->select('village_id', DB::raw('COUNT(*) as upload_count'))
            ->groupBy('village_id')
            ->get()
            ->keyBy('village_id');

        $totalVillagesUploaded = $villageUploads->count();

        // Get village details with upload counts
        $villageDetails = Village::whereIn('mandal_id', $uploadMandalIds)
            ->where('is_active', true)
            ->with('mandal')
            ->orderBy('name')
            ->get()
            ->map(function ($village) use ($villageUploads) {
                $uploadCount = $villageUploads->get($village->id)?->upload_count ?? 0;
                
                return [
                    'id' => $village->id,
                    'name' => $village->name,
                    'mandal' => $village->mandal->name,
                    'upload_count' => $uploadCount,
                ];
            });

        // Document statistics
        $totalDocumentTypes = PahaniDocument::where('is_active', true)->count();

        // Documents uploaded by current user in assigned mandals
        $documentUploads = Pahani::whereIn('mandal_id', $uploadMandalIds)
            ->select('pahani_document_id', DB::raw('COUNT(*) as upload_count'))
            ->groupBy('pahani_document_id')
            ->with('pahaniDocument')
            ->get();

        $totalDocumentsUploaded = $documentUploads->sum('upload_count');

        // Get all document types for comparison
        $allDocuments = PahaniDocument::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $documentSummary = $allDocuments->map(function ($doc) use ($documentUploads) {
            $uploadedCount = $documentUploads
                ->where('pahani_document_id', $doc->id)
                ->first()?->upload_count ?? 0;
            
            return [
                'id' => $doc->id,
                'label' => $doc->label,
                'value' => $doc->value,
                'uploaded_count' => $uploadedCount,
            ];
        });

        // Pending documents (assigned but not uploaded)
        $pendingDocuments = [];
        foreach ($allDocuments as $doc) {
            $uploadedCount = $documentUploads
                ->where('pahani_document_id', $doc->id)
                ->first()?->upload_count ?? 0;
            
            if ($uploadedCount === 0) {
                $pendingDocuments[] = [
                    'label' => $doc->label,
                    'value' => $doc->value,
                ];
            }
        }

        // Chart data for mandal uploads
        $mandalChartData = collect($mandalSummary)
            ->map(function ($mandal) {
                return [
                    'name' => $mandal['name'],
                    'uploads' => $mandal['upload_count'],
                ];
            });

        // Chart data for document uploads
        $documentChartData = $documentSummary
            ->map(function ($doc) {
                return [
                    'label' => $doc['label'],
                    'uploads' => $doc['uploaded_count'],
                ];
            });

        // Chart data for village uploads (top 10)
        $villageChartData = collect($villageDetails)
            ->sortByDesc('upload_count')
            ->take(10)
            ->map(function ($village) {
                return [
                    'name' => $village['name'] . ' (' . $village['mandal'] . ')',
                    'uploads' => $village['upload_count'],
                ];
            });

        $bb = $this->bhuBharathiReport($user);

        return view('reports.user', compact(
            'user',
            'totalMandalsAssigned',
            'totalMandalsUploaded',
            'totalVillagesAssigned',
            'totalVillagesUploaded',
            'totalDocumentTypes',
            'totalDocumentsUploaded',
            'mandalSummary',
            'villageDetails',
            'documentSummary',
            'pendingDocuments',
            'mandalChartData',
            'documentChartData',
            'villageChartData',
            'bb'
        ));
    }

        /**
     * Bhu Bharathi section of the user report.
     * Counts only disposals uploaded by this user (uploaded_by).
     */
    private function bhuBharathiReport($user): array
    {
        $perm          = $user->documentPermission;
        $assignedIds   = $perm?->getBbUploadMandalIds() ?? [];
        $permittedMods = $perm?->getBbUploadModuleIds() ?? [];

        $mine = fn () => BhuBharathi::where('uploaded_by', $user->id);
        $fmt  = fn ($dt) => $dt ? Carbon::parse($dt)->format('d-M-Y') : null;

        // ── Per mandal: uploads, distinct villages, last upload ──
        $byMandal = $mine()
            ->select(
                'mandal_id',
                DB::raw('COUNT(*) as uploads'),
                DB::raw('COUNT(DISTINCT village_id) as villages'),
                DB::raw('MAX(created_at) as last_upload')
            )
            ->groupBy('mandal_id')
            ->get()
            ->keyBy('mandal_id');

        // Assigned mandals + any mandal the user uploaded into earlier
        $mandalIds = collect($assignedIds)->merge($byMandal->keys())
            ->map(fn ($id) => (int) $id)->unique()->values();

        $villageTotals = Village::whereIn('mandal_id', $mandalIds)
            ->where('is_active', true)
            ->select('mandal_id', DB::raw('COUNT(*) as total'))
            ->groupBy('mandal_id')
            ->pluck('total', 'mandal_id');

        $mandalRows = Mandal::whereIn('id', $mandalIds)->orderBy('name')->get(['id', 'name', 'is_active'])
            ->filter(fn ($m) => $m->is_active || $byMandal->has($m->id))
            ->map(function ($m) use ($byMandal, $villageTotals, $assignedIds, $fmt) {
                $row      = $byMandal->get($m->id);
                $uploads  = (int) ($row?->uploads ?? 0);
                $covered  = (int) ($row?->villages ?? 0);
                $total    = (int) ($villageTotals[$m->id] ?? 0);
                $coverage = $total > 0 ? min(100, round($covered / $total * 100, 1)) : 0;

                return [
                    'name'              => $m->name,
                    'assigned'          => in_array((int) $m->id, $assignedIds, true),
                    'total_villages'    => $total,
                    'uploaded_villages' => $covered,
                    'uploads'           => $uploads,
                    'coverage'          => $coverage,
                    'last_upload'       => $fmt($row?->last_upload),
                    'status'            => match (true) {
                        $total > 0 && $covered >= $total => 'All Villages Covered',
                        $uploads > 0                     => 'In Progress',
                        default                          => 'Not Started',
                    },
                ];
            })
            ->values()->all();

        // ── Per module ──
        $byModule     = $mine()->select('module_id', DB::raw('COUNT(*) as uploads'))
            ->groupBy('module_id')->pluck('uploads', 'module_id');
        $totalUploads = (int) $byModule->sum();
        $share        = fn (int $n) => $totalUploads > 0 ? round($n / $totalUploads * 100, 1) : 0;

        $moduleIds = collect($permittedMods)
            ->merge($byModule->keys()->filter()->map(fn ($k) => (int) $k))
            ->unique();

        $moduleRows = Module::whereIn('id', $moduleIds)->orderBy('name')->get(['id', 'name', 'is_active'])
            ->filter(fn ($mod) => ($mod->is_active && in_array((int) $mod->id, $permittedMods, true))
                               || (int) ($byModule[$mod->id] ?? 0) > 0)
            ->map(fn ($mod) => [
                'name'      => $mod->name,
                'permitted' => in_array((int) $mod->id, $permittedMods, true),
                'uploads'   => (int) ($byModule[$mod->id] ?? 0),
                'share'     => $share((int) ($byModule[$mod->id] ?? 0)),
            ])
            ->sortByDesc('uploads')->values()->all();

        // Older disposals saved before modules were linked (module_id = NULL)
        $unlinked = (int) ($byModule[''] ?? 0);
        if ($unlinked > 0) {
            $moduleRows[] = ['name' => 'Not linked to a module', 'permitted' => true, 'uploads' => $unlinked, 'share' => $share($unlinked)];
        }

        // ── Top 20 villages ──
        $villageRows = $mine()
            ->select('village_id', 'mandal_id', DB::raw('COUNT(*) as uploads'), DB::raw('MAX(created_at) as last_upload'))
            ->groupBy('village_id', 'mandal_id')
            ->orderByDesc('uploads')
            ->limit(20)
            ->with(['village:id,name', 'mandal:id,name'])
            ->get()
            ->map(fn ($r) => [
                'name'        => $r->village?->name ?? '—',
                'mandal'      => $r->mandal?->name ?? '—',
                'uploads'     => (int) $r->uploads,
                'last_upload' => $fmt($r->last_upload),
            ])->all();

        // ── Last 10 uploads ──
        $recent = $mine()
            ->with(['mandal:id,name', 'village:id,name', 'moduleMaster:id,name'])
            ->latest()->limit(10)->get()
            ->map(fn ($r) => [
                'application_number' => $r->application_number,
                'module'             => $r->moduleMaster?->name ?? $r->module,
                'mandal'             => $r->mandal?->name ?? '—',
                'village'            => $r->village?->name ?? '—',
                'uploaded_at'        => $r->created_at?->format('d-M-Y h:i A'),
            ])->all();

        // ── Headline numbers ──
        $villagesTotal   = Village::whereIn('mandal_id', $assignedIds)->where('is_active', true)->count();
        $villagesCovered = $mine()->whereIn('mandal_id', $assignedIds)->distinct()->count('village_id');
        $bytes           = (int) $mine()->sum('file_size');
        $units           = ['B', 'KB', 'MB', 'GB'];
        $pow             = $bytes > 0 ? min((int) floor(log($bytes, 1024)), 3) : 0;

        return [
            'stats' => [
                'mandals_assigned'  => Mandal::whereIn('id', $assignedIds)->where('is_active', true)->count(),
                'mandals_uploaded'  => $byMandal->count(),
                'villages_total'    => $villagesTotal,
                'villages_uploaded' => $villagesCovered,
                'village_coverage'  => $villagesTotal > 0 ? min(100, round($villagesCovered / $villagesTotal * 100, 1)) : 0,
                'modules_permitted' => Module::active()->whereIn('id', $permittedMods)->count(),
                'modules_used'      => $byModule->keys()->filter()->count(),
                'total_uploads'     => $totalUploads,
                'this_month'        => $mine()->where('created_at', '>=', now()->startOfMonth())->count(),
                'total_size'        => round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow],
            ],
            'mandals'  => $mandalRows,
            'modules'  => $moduleRows,
            'villages' => $villageRows,
            'recent'   => $recent,
        ];
    }

    /**
     * Admin Reports Page
     * Shows all users' upload statistics and comparisons
     */
    public function adminReport()
    {
        // Get all users with their upload counts
        $users = User::with('documentPermission')
            ->get();

        $userSummaries = [];
        $totalSystemUploads = 0;
        $totalSystemMandals = 0;
        $totalSystemVillages = 0;

        foreach ($users as $user) {
            $uploadMandalIds = $user->documentPermission?->upload_mandal_ids ?? [];
            
            if (empty($uploadMandalIds)) {
                continue; // Skip users with no permissions
            }

            // User's upload count
            $userUploads = Pahani::whereIn('mandal_id', $uploadMandalIds)
                ->count();

            // Mandals assigned vs uploaded
            $assignedMandals = count($uploadMandalIds);
            $uploadedMandals = Pahani::whereIn('mandal_id', $uploadMandalIds)
                ->distinct('mandal_id')
                ->count('mandal_id');

            // Villages assigned vs uploaded
            $assignedVillages = Village::whereIn('mandal_id', $uploadMandalIds)
                ->where('is_active', true)
                ->count();

            $uploadedVillages = Pahani::whereIn('mandal_id', $uploadMandalIds)
                ->distinct('village_id')
                ->count('village_id');

            $userSummaries[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'assigned_mandals' => $assignedMandals,
                'uploaded_mandals' => $uploadedMandals,
                'assigned_villages' => $assignedVillages,
                'uploaded_villages' => $uploadedVillages,
                'total_uploads' => $userUploads,
                'completion_percentage' => $assignedVillages > 0 ? round(($uploadedVillages / $assignedVillages) * 100, 2) : 0,
            ];

            $totalSystemUploads += $userUploads;
            $totalSystemMandals += $assignedMandals;
            $totalSystemVillages += $assignedVillages;
        }

        // Sort by uploads descending
        usort($userSummaries, function ($a, $b) {
            return $b['total_uploads'] <=> $a['total_uploads'];
        });

        // Mandal-wise uploads across all users with enhanced details
        $allMandals = Mandal::where('is_active', true)->get();
        $documentTypesPerVillage = PahaniDocument::where('is_active', true)->count();
        
        $mandalUploads = $allMandals->map(function ($mandal) use ($documentTypesPerVillage) {
            $totalVillages = Village::where('mandal_id', $mandal->id)
                ->where('is_active', true)
                ->count();

            $uploadedVillages = Pahani::where('mandal_id', $mandal->id)
                ->distinct('village_id')
                ->count('village_id');

            $totalDocuments = $totalVillages * $documentTypesPerVillage;

            $uploadedDocuments = Pahani::where('mandal_id', $mandal->id)->where('physical_document', 'yes')->count();

            $unAvailableDocuments = Pahani::where('mandal_id', $mandal->id)->where('physical_document', 'no')->count();

            $completionPercentage = $totalDocuments > 0
                ? round(($uploadedDocuments / $totalDocuments) * 100, 2)
                : 0;

            return [
                'id' => $mandal->id,
                'mandal' => $mandal->name,
                'total_villages' => $totalVillages,
                'uploaded_villages' => $uploadedVillages,
                'total_documents' => $totalDocuments,
                'uploaded_documents' => $uploadedDocuments,
                'unavailable_documents' => $unAvailableDocuments,
                'completion_percentage' => $completionPercentage,
                'uploads' => $uploadedDocuments, // kept so existing sort/chart code still works
            ];
        })
        ->sortByDesc('uploads')
        ->values();

        $totalSystemDocuments = $mandalUploads->sum('total_documents');

        $mandalUploads = $mandalUploads->map(function ($item) use ($totalSystemDocuments) {
            $item['percentage_of_system'] = $totalSystemDocuments > 0
                ? round(($item['uploaded_documents'] / $totalSystemDocuments) * 100, 2)
                : 0;
            return $item;
        });

        // Document-wise uploads across all users
        $documentUploads = Pahani::select('pahani_document_id', DB::raw('COUNT(*) as upload_count'))
            ->groupBy('pahani_document_id')
            ->with('pahaniDocument')
            ->get()
            ->map(function ($item) {
                return [
                    'document' => $item->pahaniDocument->label,
                    'uploads' => $item->upload_count,
                ];
            })
            ->sortByDesc('uploads')
            ->values();

        // Overall statistics
        $totalDocumentTypes = PahaniDocument::where('is_active', true)->count();
        $totalActiveMandals = Mandal::where('is_active', true)->count();
        $totalActiveVillages = Village::where('is_active', true)->count();
        $totalActiveUsers = $users->count();

        // Chart data
        $userChartData = collect($userSummaries)
            ->map(function ($user) {
                return [
                    'name' => $user['name'],
                    'uploads' => $user['total_uploads'],
                ];
            })
            ->sortByDesc('uploads')
            ->take(15)
            ->values();

        $mandalChartData = $mandalUploads->take(10);

        $documentChartData = $documentUploads->take(15);

        return view('reports.admin', compact(
            'userSummaries',
            'mandalUploads',
            'documentUploads',
            'totalSystemUploads',
            'totalSystemMandals',
            'totalSystemVillages',
            'totalDocumentTypes',
            'totalActiveMandals',
            'totalActiveVillages',
            'totalActiveUsers',
            'userChartData',
            'mandalChartData',
            'documentChartData'
        ));
    }
}