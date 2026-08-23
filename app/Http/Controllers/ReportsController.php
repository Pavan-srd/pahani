<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pahani;
use App\Models\Mandal;
use App\Models\Village;
use App\Models\PahaniDocument;
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
            'villageChartData'
        ));
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

            $uploadedDocuments = Pahani::where('mandal_id', $mandal->id)->count();

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