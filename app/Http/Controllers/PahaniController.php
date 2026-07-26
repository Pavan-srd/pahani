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

class PahaniController extends Controller
{
    // ── INDEX (upload form) ───────────────────────────────────────────────────
    public function index()
    {
        $mandals  = Mandal::where('is_active', true)->orderBy('name')->get();
        $documents = PahaniDocument::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'value', 'label', 'type', 'description']);

        return view('index', compact('mandals', 'documents'));
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
        // dd($request);
        // ── 1. Validate top-level fields ──────────────────────────────────────
        $request->validate([
            'mandal'  => ['required', 'string', 'exists:mandals,slug'],
            'village' => ['required', 'string'],
            'records' => ['required', 'json'],
        ]);

        $records = json_decode($request->input('records'), true);

        if (empty($records) || !is_array($records)) {
            logger('Pahani store failed: no records submitted');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one document record is required.',
                    'errors'  => ['records' => ['At least one document record is required.']],
                ], 422);
            }
            return back()->withErrors(['records' => 'At least one document record is required.']);
        }

        // ── 2. Resolve Mandal & Village models ────────────────────────────────
        $mandal = Mandal::where('slug', $request->mandal)
            ->where('is_active', true)
            ->firstOrFail();

        $village = Village::where('mandal_id', $mandal->id)
            ->where('slug', $request->village)
            ->where('is_active', true)
            ->firstOrFail();

        // ── 3. Validate each record row ───────────────────────────────────────
        $allowedDocValues = PahaniDocument::where('is_active', true)
            ->pluck('value')
            ->toArray();

        $errors   = [];
        $cleaned  = [];

        foreach ($records as $i => $row) {
            $docValue = $row['docValue'] ?? null;
            $physical = strtolower($row['physical'] ?? '');
            $keepFile = (bool) ($row['keepFile'] ?? false);
            $rowNum   = $i + 1;

            if (!in_array($docValue, $allowedDocValues)) {
                $errors[] = "Row {$rowNum}: Invalid document type '{$docValue}'.";
                continue;
            }

            if (!in_array($physical, ['yes', 'no'])) {
                $errors[] = "Row {$rowNum}: Physical document must be 'yes' or 'no'.";
                continue;
            }

            $uploadedFile = null;
            if ($physical === 'yes') {
                $uploadedFile = $request->file("files.{$docValue}");

                if (!$uploadedFile && !$keepFile) {
                    $errors[] = "Row {$rowNum}: File is required when physical document is 'yes'.";
                    continue;
                }

                if ($uploadedFile) {
                    if ($uploadedFile->getMimeType() !== 'application/pdf') {
                        $errors[] = "Row {$rowNum}: Only PDF files are allowed.";
                        continue;
                    }
                    if ($uploadedFile->getSize() > 5 * 1024 * 1024) {
                        $errors[] = "Row {$rowNum}: File size must not exceed 5 MB.";
                        continue;
                    }
                }
            }

            $cleaned[] = [
                'docValue' => $docValue,
                'physical' => $physical,
                'file'     => $uploadedFile,
                'keepFile' => $keepFile,
            ];
        }

        if (!empty($errors)) {
            logger('Pahani store validation errors', [
                'mandal' => $mandal->slug, 'village' => $village->slug, 'errors' => $errors,
            ]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the errors below.',
                    'errors'  => $errors,
                ], 422);
            }
            return back()->withErrors($errors)->withInput();
        }

        // ── 4. Save inside a transaction ──────────────────────────────────────
        DB::beginTransaction();
        try {
            foreach ($cleaned as $row) {
                // Resolve pahani_documents record
                $pahaniDoc = PahaniDocument::where('value', $row['docValue'])->firstOrFail();

                // // Build file info
                // $fileName = null;
                // $filePath = null;
                // $fileSize = null;
                // $fileMime = null;

                // if ($row['physical'] === 'yes' && $row['file']) {
                //     $file = $row['file'];

                //     // R2 object key:
                //     // pahani/{mandal_slug}/{village_slug}/{docValue}/{uuid}.pdf
                //     $r2Key = implode('/', [
                //         'pahani',
                //         $mandal->slug,
                //         $village->slug,
                //         $row['docValue'],
                //         Str::uuid() . '.pdf',
                //     ]);

                //     // Upload to Cloudflare R2 (configured as disk 'r2' in filesystems.php)
                //     Storage::disk('r2')->put($r2Key, file_get_contents($file->getRealPath()));

                //     $fileName = $file->getClientOriginalName();
                //     $filePath = $r2Key;
                //     $fileSize = $file->getSize();
                //     $fileMime = $file->getMimeType();
                // }

                // // Upsert — if the same village+document was submitted before, update it
                // Pahani::updateOrCreate(
                //     [
                //         'village_id'          => $village->id,
                //         'pahani_document_id'  => $pahaniDoc->id,
                //     ],
                //     [
                //         'mandal_id'           => $mandal->id,
                //         'document_name'       => $pahaniDoc->label,
                //         'type'                => $pahaniDoc->type,
                //         'physical_document'   => $row['physical'],
                //         'file_name'           => $fileName,
                //         'file_path'           => $filePath,
                //         'file_size'           => $fileSize,
                //         'file_mime'           => $fileMime,
                //         'disk'                => 'r2',
                //         'uploaded_by'         => auth()->id() ?? 'guest',
                //         'uploaded_ip'         => $request->ip(),
                //     ]
                // );

                $fileName = null;
                $filePath = null;
                $fileSize = null;
                $fileMime = null;
            
                // Check if there's an existing record to potentially keep its file
                $existing = Pahani::where('village_id', $village->id)
                    ->where('pahani_document_id', $pahaniDoc->id)
                    ->first();
            
                if ($row['physical'] === 'yes') {
                    if ($row['file']) {
                        // New file uploaded — store to R2
                        $file  = $row['file'];
                        $r2Key = implode('/', [
                            'pahani', $mandal->slug, $village->slug,
                            $row['docValue'], Str::uuid() . '.pdf',
                        ]);
                        Storage::disk('r2')->put($r2Key, file_get_contents($file->getRealPath()));

                        // Delete old R2 file if replacing
                        if ($existing?->file_path) {
                            Storage::disk('r2')->delete($existing->file_path);
                        }
            
                        $fileName = $file->getClientOriginalName();
                        $filePath = $r2Key;
                        $fileSize = $file->getSize();
                        $fileMime = $file->getMimeType();
            
                    } elseif ($row['keepFile'] && $existing) {
                        // No new upload — keep the existing file info untouched
                        $fileName = $existing->file_name;
                        $filePath = $existing->file_path;
                        $fileSize = $existing->file_size;
                        $fileMime = $existing->file_mime;
                    }
                    // else: physical=yes but no file and no keepFile — validation should have caught this
                }
                // physical=no: file columns stay null (delete old file from R2 if any)
                elseif ($existing?->file_path) {
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
            
                // Also add 'keepFile' to the $cleaned array in step 3:
                $cleaned[] = [
                    'docValue' => $docValue,
                    'physical' => $physical,
                    'file'     => $uploadedFile,
                    'keepFile' => (bool) ($row['keepFile'] ?? false),
                ];
            
                // And relax the file validation to allow keepFile=true with no new upload:
                if ($physical === 'yes' && !$uploadedFile) {
                    $keepFile = (bool)($row['keepFile'] ?? false);
                    if (!$keepFile) {
                        // Check if there's already a saved file for this doc in this village
                        $existingRec = Pahani::where('village_id', $village->id)->first(); // resolve village_id here if needed
                        // Simpler: just skip the error if keepFile is true
                        $errors[] = "Row {$rowNum}: File is required when physical document is 'yes'.";
                        continue;
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Pahani store failed');
            $msg = 'Submission failed. Error: ' . ($e->getPrevious()?->getMessage() ?? $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return back()->withErrors(['submit' => $msg])->withInput();
        }

        $msg = 'Pahani records saved successfully for ' . $village->name . ', ' . $mandal->name . '.';
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('pahani.index')
            ->with('success', 'Pahani records saved successfully for ' . $village->name . ', ' . $mandal->name . '.');
    }

    // ── VIEW (records display page) ───────────────────────────────────────────
    public function view(Request $request)
    {
        $mandals = Mandal::where('is_active', true)->orderBy('name')->get();

        $records  = collect();
        $mandal   = null;
        $village  = null;

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

        return view('pahani.view', compact('mandals', 'records', 'mandal', 'village'));
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
            ->where('uploaded_by', auth()->id() ?? 0)
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
        if (!$pahani->file_path) {
            abort(404, 'No file uploaded for this record.');
        }
    
        return view('pahani.pdf-viewer', [
            'pahani'       => $pahani,
            'pdfSourceUrl' => route('pahani.pdf-source', $pahani),
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
}