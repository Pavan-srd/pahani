<?php

namespace App\Http\Controllers;

use App\Models\Mandal;
use App\Models\Village;
use App\Models\PahaniDocument;
use App\Models\Pahani;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PahaniController extends Controller
{
    // ── INDEX (upload form) ───────────────────────────────────────────────────
    public function index()
    {
        $user = Auth::user();

        if ($user) {
            $mandals = $user->mandals()
                ->select('mandals.*')
                ->where('mandals.is_active', true)
                ->orderBy('mandals.name')
                ->get();
        } else {
            $mandals = collect();
        }

        $documents = PahaniDocument::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'value', 'label', 'type', 'description']);

        return view('index', compact('mandals', 'documents', 'user'));
    }

    // ── STORE (form submit) ───────────────────────────────────────────────────
    /**
     * Expected multipart/form-data payload from the blade form:
     *
     * mandal   = "sangareddy"          (slug)
     * village  = "kandi"               (slug)
     * records  = JSON string of array:
     * [
     *   {
     *     "docValue":  "sethwar",          // pahani_documents.value
     *     "physical":  "yes" | "no",
     *     "fileName":  "Sethwar_Kandi.pdf" // original name (sent separately in files)
     *   },
     *   ...
     * ]
     * files[sethwar]   = <UploadedFile>   // key = docValue
     * files[yr_1985]   = <UploadedFile>
     * ...
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'mandal'  => ['required', 'string', 'exists:mandals,slug'],
            'village' => ['required', 'string'],
            'records' => ['required', 'array', 'min:1'],
        ]);

        $mandal = Mandal::where('slug', $data['mandal'])->where('is_active', true)->firstOrFail();
        $village = Village::where('mandal_id', $mandal->id)
            ->where('slug', $data['village'])
            ->where('is_active', true)
            ->firstOrFail();

        $allowedDocValues = PahaniDocument::where('is_active', true)->pluck('value')->toArray();
        $errors = [];
        $cleaned = [];

        foreach ($data['records'] as $i => $row) {
            $rowNum   = $i + 1;
            $docValue = $row['docValue'] ?? null;
            $physical = strtolower($row['physical'] ?? '');
            $keepFile = (bool) ($row['keepFile'] ?? false);
            $r2Key    = $row['r2Key'] ?? null;

            if (!in_array($docValue, $allowedDocValues)) {
                $errors[] = "Row {$rowNum}: Invalid document type.";
                continue;
            }
            if (!in_array($physical, ['yes', 'no'])) {
                $errors[] = "Row {$rowNum}: Physical document must be 'yes' or 'no'.";
                continue;
            }

            if ($physical === 'yes') {
                // Must have either a freshly uploaded key, or be explicitly keeping the old file
                if (!$r2Key && !$keepFile) {
                    $errors[] = "Row {$rowNum}: File is required when physical document is 'yes'.";
                    continue;
                }
                // Guard against a forged/mismatched key: it must live under this mandal/village/doc path
                if ($r2Key) {
                    $expectedPrefix = "pahani/{$mandal->slug}/{$village->slug}/{$docValue}/";
                    if (!Str::startsWith($r2Key, $expectedPrefix) || !Storage::disk('r2')->exists($r2Key)) {
                        $errors[] = "Row {$rowNum}: Uploaded file could not be verified.";
                        continue;
                    }
                }
            }

            $cleaned[] = compact('docValue', 'physical', 'r2Key', 'keepFile') + [
                'fileName' => $row['fileName'] ?? null,
                'fileSize' => $row['fileSize'] ?? null,
                'fileMime' => $row['fileMime'] ?? null,
            ];
        }

        if (!empty($errors)) {
            return response()->json(['success' => false, 'message' => 'Please fix the errors below.', 'errors' => $errors], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($cleaned as $row) {
                $pahaniDoc = PahaniDocument::where('value', $row['docValue'])->firstOrFail();

                $existing = Pahani::where('village_id', $village->id)
                    ->where('pahani_document_id', $pahaniDoc->id)
                    ->first();

                $fileName = $filePath = $fileSize = $fileMime = null;

                if ($row['physical'] === 'yes') {
                    if ($row['r2Key']) {
                        // New file already sitting in R2 — delete the old one if replacing
                        if ($existing?->file_path && $existing->file_path !== $row['r2Key']) {
                            Storage::disk('r2')->delete($existing->file_path);
                        }
                        $fileName = $row['fileName'];
                        $filePath = $row['r2Key'];
                        $fileSize = $row['fileSize'];
                        $fileMime = $row['fileMime'];
                    } elseif ($row['keepFile'] && $existing) {
                        $fileName = $existing->file_name;
                        $filePath = $existing->file_path;
                        $fileSize = $existing->file_size;
                        $fileMime = $existing->file_mime;
                    }
                } elseif ($existing?->file_path) {
                    Storage::disk('r2')->delete($existing->file_path);
                }

                Pahani::updateOrCreate(
                    ['village_id' => $village->id, 'pahani_document_id' => $pahaniDoc->id],
                    [
                        'mandal_id'         => $mandal->id,
                        'document_name'     => $pahaniDoc->label,
                        'type'              => $pahaniDoc->type,
                        'physical_document' => $row['physical'],
                        'file_name'         => $fileName,
                        'file_path'         => $filePath,
                        'file_size'         => $fileSize,
                        'file_mime'         => $fileMime,
                        'disk'              => 'r2',
                        'uploaded_by'       => auth()->id() ?? 'guest',
                        'uploaded_ip'       => $request->ip(),
                    ]
                );
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Pahani store failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Submission failed. Please try again.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Pahani records saved successfully for {$village->name}, {$mandal->name}.",
        ]);
    }

    public function presign(Request $request)
    {
        $request->validate([
            'mandal'   => ['required', 'string', 'exists:mandals,slug'],
            'village'  => ['required', 'string'],
            'docValue' => ['required', 'string', 'exists:pahani_documents,value'],
            'fileMime' => ['required', 'string'],
        ]);

        $mandal = Mandal::where('slug', $request->mandal)->where('is_active', true)->firstOrFail();
        $village = Village::where('mandal_id', $mandal->id)
            ->where('slug', $request->village)
            ->where('is_active', true)
            ->firstOrFail();

        if ($request->fileMime !== 'application/pdf') {
            return response()->json(['success' => false, 'message' => 'Only PDF files are allowed.'], 422);
        }

        $key = implode('/', [
            'pahani', $mandal->slug, $village->slug,
            $request->docValue, Str::uuid() . '.pdf',
        ]);

        $signed = Storage::disk('r2')->temporaryUploadUrl(
            $key,
            now()->addMinutes(20),
            ['ContentType' => $request->fileMime]
        );

        return response()->json([
            'success' => true,
            'key'     => $key,
            'url'     => $signed['url'],
            'headers' => $signed['headers'] ?? ['Content-Type' => $request->fileMime],
        ]);
    }

    // ── VIEW (records display page) ───────────────────────────────────────────
    public function view(Request $request)
    {
        $mandals = Mandal::where('is_active', true)->orderBy('name')->get();
 
        $records  = collect();
        $mandal   = null;
        $village  = null;

        $permissions = $this->getUserPermissions();
 
        if ($request->filled('mandal') && $request->filled('village')) {
            $mandal = Mandal::where('slug', $request->mandal)->firstOrFail();
            $village = Village::where('mandal_id', $mandal->id)
                ->where('slug', $request->village)
                ->firstOrFail();
 
            $records = Pahani::with('pahaniDocument')
                ->where('mandal_id', $mandal->id)
                ->where('village_id', $village->id)
                ->orderBy(
                    PahaniDocument::select('sort_order')
                        ->whereColumn('pahani_documents.id', 'pahanis.pahani_document_id')
                        ->limit(1)
                )
                ->get();
        }
 
        return view('pahani.view', compact('mandals', 'records', 'mandal', 'village', 'permissions'));
    }

    private function getUserPermissions()
    {
        $user = auth()->user();
        
        // Admin has all permissions
        if ($user->role === 'admin') {
            return [
                'can_view' => true,
                'can_edit' => true,
            ];
        }
        
        // Regular users get permissions from database
        $permissions = $user->getOrCreateDocumentPermission();
        return [
            'can_view' => $permissions->can_view,
            'can_edit' => $permissions->can_edit,
        ];
    }

    // ── SHOW PDF (serve from R2 via signed URL or proxy) ─────────────────────
    public function showPdf(Pahani $pahani)
    {
        abort_if(!$pahani->file_path, 404, 'No file attached to this record.');

        /*
         * Option A — Temporary URL (recommended for R2 with public-bucket disabled).
         * Generates a pre-signed URL valid for 30 minutes.
         */
        $url = Storage::disk('r2')->temporaryUrl($pahani->file_path, now()->addMinutes(30));
        return redirect($url);

        /*
         * Option B — Proxy the file through Laravel (no signed URL needed).
         * Uncomment below and comment out Option A if preferred.
         *
         * $stream = Storage::disk('r2')->readStream($pahani->file_path);
         * return response()->stream(function () use ($stream) {
         *     fpassthru($stream);
         * }, 200, [
         *     'Content-Type'        => 'application/pdf',
         *     'Content-Disposition' => 'inline; filename="' . $pahani->file_name . '"',
         * ]);
         */
    }

    /**
     * AJAX — return villages for a given mandal.
     * Called by the blade when user changes the Mandal dropdown.
     *
     * GET /api/mandals/{mandal}/villages
     */
    public function apiVillages(Mandal $mandal): \Illuminate\Http\JsonResponse
    {
        $villages = $mandal->villages()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    
        return response()->json($villages);
    }
    
    /**
     * AJAX — return existing pahani records for a village so the form
     * can pre-populate rows and show already-saved files.
     *
     * GET /api/villages/{village}/pahanis
     */
    public function apiVillagePahanis(Village $village): \Illuminate\Http\JsonResponse
    {
        $records = Pahani::with('pahaniDocument')
            ->where('village_id', $village->id)
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($p) => [
                'id'               => $p->id,
                'document_value'   => $p->pahaniDocument->value,   // e.g. "sethwar" / "yr_1985"
                'document_name'    => $p->document_name,            // e.g. "Sethwar Pahani"
                'type'             => $p->type,                     // "core" / "year"
                'physical_document'=> $p->physical_document,        // "yes" / "no"
                'file_name'        => $p->file_name,                // original filename
                'file_path'        => $p->file_path,                // R2 object key
                'file_size_human'  => $p->file_size_human,          // "1.23 MB"
                'uploaded_by'      => $p->uploaded_by, 
            ]);
    
        return response()->json($records);
    }

    public function showFile(Pahani $pahani)
    {
        if (!$pahani->file_path) {
            abort(404, 'No file uploaded for this record.');
        }

        $disk = $pahani->disk ?? 'r2';

        if (!Storage::disk($disk)->exists($pahani->file_path)) {
            abort(404, 'File not found in storage.');
        }

        // Option A (recommended): redirect to a short-lived R2 presigned URL.
        // Works because R2 is S3-compatible — no bandwidth cost to your server.
        return redirect(
            Storage::disk($disk)->temporaryUrl($pahani->file_path, now()->addMinutes(10))
        );

        // Option B (fallback if temporaryUrl isn't supported on your disk config):
        // stream it through Laravel instead — uncomment and remove Option A above.
        /*
        $stream = Storage::disk($disk)->readStream($pahani->file_path);
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type'        => $pahani->file_mime ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pahani->file_name . '"',
        ]);
        */
    }

    /**
     * GET /pahani/view-pdf/{pahani}
     * Renders the in-site secure viewer page (site header/footer + PDF.js canvas).
     */
    public function viewPdfPage(Pahani $pahani)
    {
        // ── Check 1: Document has file path ──
        if (!$pahani->file_path) {
            abort(404, 'No file uploaded for this record.');
        }
 
        // ── Check 2: User has permission ──
        $this->authorizeUserAccess($pahani);
 
        // ── Check 3: File exists in R2 ──
        if (!Storage::disk('r2')->exists($pahani->file_path)) {
            Log::error('PDF file not found in R2', [
                'pahani_id' => $pahani->id,
                'file_path' => $pahani->file_path,
            ]);
            abort(404, 'File not found in Cloudflare R2.');
        }
 
        // ── Check 4: Generate secure signed URL ──
        try {
            $expiresAt = Carbon::now()->addMinutes(
                (int) config('filesystems.disks.r2.signed_url_expires', 60)
            );
            
            // Generate Cloudflare R2 signed URL
            // This URL is valid for 60 minutes and includes cryptographic signature
            $cloudflareUrl = Storage::disk('r2')->temporaryUrl(
                $pahani->file_path,
                $expiresAt
            );
 
        } catch (\Exception $e) {
            Log::error('Failed to generate signed URL', [
                'user_id' => Auth::id(),
                'pahani_id' => $pahani->id,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Unable to generate secure URL');
        }
 
        return view('pahani.pdf-viewer', [
            'pahani'       => $pahani,
            'pdfSourceUrl' => $cloudflareUrl,  // ← Direct Cloudflare URL
        ]);
    }
    
    /**
     * GET /pahani/pdf-source/{pahani}
     * Streams the raw PDF bytes same-origin (so PDF.js can fetch() it without
     * hitting CORS issues from a cross-origin R2 presigned URL), with headers
     * that avoid triggering a native "Save As" prompt.
     */
    public function pdfSource(Pahani $pahani)
    {
        if (!$pahani->file_path) {
            abort(404, 'No file uploaded for this record.');
        }
    
        $disk = $pahani->disk ?? 'r2';
    
        if (!Storage::disk($disk)->exists($pahani->file_path)) {
            abort(404, 'File not found in storage.');
        }
    
        $stream = Storage::disk($disk)->readStream($pahani->file_path);
    
        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'        => 'application/pdf',
            // No filename in Content-Disposition — reduces the "Save As" hint browsers show.
            'Content-Disposition' => 'inline',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
            'X-Frame-Options'     => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function getSignedUrl(Request $request, Pahani $pahani)
    {
        try {
            // ── Check: User authenticated (middleware) ──
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
 
            // ── Check: Document has file ──
            if (!$pahani->file_path) {
                return response()->json(['error' => 'File not found'], 404);
            }
 
            // ── Check: User has permission ──
            try {
                $this->authorizeUserAccess($pahani);
            } catch (\Exception $e) {
                Log::warning('PDF URL access denied - no permission', [
                    'user_id' => Auth::id(),
                    'pahani_id' => $pahani->id,
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }
 
            // ── Check: File exists in R2 ──
            if (!Storage::disk('r2')->exists($pahani->file_path)) {
                return response()->json(['error' => 'File not found in storage'], 404);
            }
 
            // ── Generate signed URL ──
            $expiresAt = Carbon::now()->addMinutes(
                (int) config('filesystems.disks.r2.signed_url_expires', 60)
            );
 
            $cloudflareUrl = Storage::disk('r2')->temporaryUrl(
                $pahani->file_path,
                $expiresAt
            );
 
            return response()->json([
                'success' => true,
                'url' => $cloudflareUrl,
                'filename' => $pahani->file_name,
                'expires_at' => $expiresAt->toDateTimeString(),
                'expires_in_minutes' => (int) config('filesystems.disks.r2.signed_url_expires', 60),
            ]);
 
        } catch (\Exception $e) {
            Log::error('Error generating signed URL', [
                'user_id' => Auth::id() ?? 'guest',
                'pahani_id' => $pahani->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Redirect to Cloudflare R2 signed URL
     * GET /pahani/pdf-redirect/{pahani}
     * 
     * Direct redirect to Cloudflare URL in browser
     * Browser opens PDF in new tab/window directly from Cloudflare
     * 
     * Usage:
     * <a href="{{ route('pahani.pdf-redirect', $pahani) }}" target="_blank">
     *   View PDF in Cloudflare
     * </a>
     */
    public function redirectToCloudflare(Pahani $pahani)
    {
        // ── Check: User authenticated ──
        if (!Auth::check()) {
            abort(401, 'Unauthorized');
        }
 
        // ── Check: Document has file ──
        if (!$pahani->file_path) {
            abort(404, 'No file uploaded for this record.');
        }
 
        // ── Check: User has permission ──
        $this->authorizeUserAccess($pahani);
 
        // ── Check: File exists in R2 ──
        if (!Storage::disk('r2')->exists($pahani->file_path)) {
            abort(404, 'File not found in storage.');
        }
 
        // ── Generate signed URL ──
        $expiresAt = Carbon::now()->addMinutes(
            (int) config('filesystems.disks.r2.signed_url_expires', 60)
        );
        $cloudflareUrl = Storage::disk('r2')->temporaryUrl(
            $pahani->file_path,
            $expiresAt
        );
 
        // ── Redirect to Cloudflare R2 URL ──
        return redirect($cloudflareUrl);
    }
    
 
    /**
     * Verify user has permission to access this document
     * 
     * Checks:
     * 1. User is owner of document
     * 2. User is admin
     * 3. User's mandals include this document's mandal
     */
    private function authorizeUserAccess(Pahani $pahani)
    {
        $user = Auth::user();
 
        // ── Check: User owns document OR is admin ──
        if ($pahani->uploaded_by === $user->id || $user->role === 'admin') {
            return true;
        }
 
        // ── Check: User's mandals include this document's mandal ──
        $userMandals = $user->mandals()->pluck('mandals.id')->toArray();
        
        if (in_array($pahani->mandal_id, $userMandals)) {
            return true;
        }
 
        // ── Access denied ──
        throw new \Exception('Unauthorized document access');
    }

    public function updateFile(Request $request, Pahani $pahani)
    {
        // Only allow editing if user can edit
        if (!auth()->user()->canEditDocuments()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit documents.'
            ], 403);
        }

        $request->validate([
            'r2_key'           => ['required', 'string'],
            'file_name'        => ['required', 'string'],
            'file_size'        => ['required', 'integer'],
            'file_mime'        => ['required', 'string'],
            'old_file_path'    => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            // Delete old file from R2 if it exists
            if (!empty($request->old_file_path)) {
                try {
                    Storage::disk('r2')->delete($request->old_file_path);
                } catch (\Throwable $e) {
                    \Log::warning('Failed to delete old file from R2: ' . $e->getMessage());
                    // Continue anyway - file might already be deleted
                }
            }

            // Update the Pahani record
            $pahani->update([
                'file_name'  => $request->file_name,
                'file_path'  => $request->r2_key,
                'file_size'  => $request->file_size,
                'file_mime'  => $request->file_mime,
                'disk'       => 'r2',
                'uploaded_by'=> auth()->id(),
                'uploaded_ip'=> $request->ip(),
            ]);

            // Calculate human-readable file size
            $fileSizeHuman = $this->formatFileSize($request->file_size);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PDF updated successfully.',
                'pahani'  => [
                    'id'              => $pahani->id,
                    'file_name'       => $pahani->file_name,
                    'file_path'       => $pahani->file_path,
                    'file_size_human' => $fileSizeHuman,
                    'uploaded_by'     => $pahani->uploaded_by,
                ],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Failed to update Pahani file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update PDF. Please try again.'
            ], 500);
        }
    }

    /**
     * Format bytes to human readable size
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

}