<?php

namespace App\Http\Controllers;

use App\Models\BhuBharathi;
use App\Models\Mandal;
use App\Models\Module;
use App\Models\Village;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Bhu Bharathi Disposals
 *
 * Permissions (stored on the user's UserDocumentPermission row, next to Pahani's):
 *   bb_upload_mandal_ids → can add new disposals in these mandals
 *   bb_view_mandal_ids   → can see all disposals (and open PDFs) in these mandals
 *   bb_edit_mandal_ids   → can edit any disposal in these mandals
 * The View (open PDF) and Edit buttons are strictly permission-based:
 *   View is allowed only for mandals in bb_view_mandal_ids,
 *   Edit is allowed only for mandals in bb_edit_mandal_ids.
 * Module upload permission (bb_upload_module_ids):
 *   the Module dropdown only lists modules the admin ticked for this user,
 *   and store()/update() only accept those modules.
 *
 * Files go browser → R2 directly (presigned PUT, or multipart for large files),
 * then only a small JSON payload is sent to store()/update().
 */
class BhuBharathiController extends Controller
{
    private const DISK     = 'r2';
    private const KEY_ROOT = 'bhu-bharathi';
    private const MIME     = 'application/pdf';
    private const MAX_ROWS = 100;   // disposals per submission

    private ?array $permCache = null;
    private ?array $moduleCache = null;

    // ── PAGE ─────────────────────────────────────────────────────────────────
    public function index()
    {
        $user    = Auth::user();
        $mandals = collect();

        if ($user) {
            // Same UserDocumentPermission row used for Pahani
            $documentPermission = $user->documentPermission;

            // Bhu Bharathi upload mandal IDs given by admin (JSON array)
            $uploadMandalIds = $documentPermission?->getBbUploadMandalIds() ?? [];

            if (!empty($uploadMandalIds)) {
                $mandals = Mandal::whereIn('id', $uploadMandalIds)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug']);
            }
        }

        // Module dropdown: only active modules the admin gave this user UPLOAD permission for
        $modules = $this->allowedModules()
            ->map(fn ($name, $id) => ['id' => (int) $id, 'name' => $name])
            ->values();

        return view('bhu_bharathi', [
            'mandals'     => $mandals,
            'modules'     => $modules,
            'permissions' => $this->permissions(),   // upload / view / edit IDs for the JS
            'user'        => $user,
        ]);
    }

    // ── MY UPLOADED FILES (separate "View Bhu Bharathi Disposals" page) ───────
    public function myFiles(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $records = BhuBharathi::with([
                'mandal:id,name,slug',
                'village:id,name,slug',
                'moduleMaster:id,name',
                'uploader:id,name',
            ])
            ->where('uploaded_by', Auth::id())
            ->when($q !== '', function ($query) use ($q) {
                // escape LIKE wildcards so "%" or "_" in a search are matched literally
                $query->where('application_number', 'like', '%' . addcslashes($q, '%_\\') . '%');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Rows for the table + edit popup (same shape as the AJAX records)
        $rows = $records->getCollection()->map(fn (BhuBharathi $r) => $this->present($r))->values();

        // Module dropdown in the edit popup: modules this user may upload
        $modules = $this->allowedModules()
            ->map(fn ($name, $id) => ['id' => (int) $id, 'name' => $name])
            ->values();

        return view('bhu_bharathi_files', compact('records', 'rows', 'modules', 'q'));
    }

    // ── EXISTING RECORDS FOR A VILLAGE (AJAX) ────────────────────────────────
    public function records(Request $request): JsonResponse
    {
        $request->validate([
            'mandal'  => ['required', 'string'],
            'village' => ['required', 'string'],
        ]);

        [$mandal, $village] = $this->resolveLocation($request->mandal, $request->village);

        $canSeeAll = $this->canViewMandal($mandal->id);

        if (!$canSeeAll && !$this->hasPerm('upload', $mandal->id)) {
            return response()->json(['success' => false, 'message' => 'You do not have access to this mandal.'], 403);
        }

        $records = BhuBharathi::with('uploader:id,name')
            ->where('mandal_id', $mandal->id)
            ->where('village_id', $village->id)
            ->when(!$canSeeAll, fn ($q) => $q->where('uploaded_by', Auth::id()))
            ->latest()
            ->get()
            ->map(fn (BhuBharathi $r) => $this->present($r));

        return response()->json([
            'success'  => true,
            'scope'    => $canSeeAll ? 'all' : 'own',
            'records'  => $records,
        ]);
    }

    // ── STORE (bulk: every row has its own mandal / village / module) ──────────
    /**
     * JSON payload:
     * {
     *   records: [
     *     { mandal_id, village_id, module_id, application_number, r2Key, fileName, fileSize }, ...
     *   ]
     * }
     * Each row = one PDF = one record.
     * Application number must be unique per mandal + village + module.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'records'   => ['required', 'array', 'min:1', 'max:' . self::MAX_ROWS],
            'records.*' => ['array'],
        ], [
            'records.required' => 'Add at least one disposal record.',
            'records.max'      => 'You can submit at most ' . self::MAX_ROWS . ' disposals at a time.',
        ]);

        $rows = array_values($data['records']);

        // Load every mandal / village used in this submission in two queries
        $ids = fn (string $field) => collect($rows)->pluck($field)->map(fn ($v) => (int) $v)->filter()->unique()->values();

        $mandals  = Mandal::whereIn('id', $ids('mandal_id'))->where('is_active', true)
            ->get(['id', 'name', 'slug'])->keyBy('id');
        $villages = Village::whereIn('id', $ids('village_id'))->where('is_active', true)
            ->get(['id', 'name', 'slug', 'mandal_id'])->keyBy('id');
        $modules  = $this->allowedModules();               // [id => name] — admin-permitted, active

        $errors    = [];
        $cleaned   = [];
        $seenCombo = [];
        $seenKeys  = [];

        foreach ($rows as $i => $row) {
            $n         = $i + 1;
            $mandalId  = (int) ($row['mandal_id'] ?? 0);
            $villageId = (int) ($row['village_id'] ?? 0);
            $moduleId  = (int) ($row['module_id'] ?? 0);
            $appNo     = trim((string) ($row['application_number'] ?? ''));
            $key       = (string) ($row['r2Key'] ?? '');
            $mandal    = $mandals->get($mandalId);
            $village   = $villages->get($villageId);

            if (!$mandal) {
                $errors[] = "Row {$n}: Please select a valid mandal.";
            } elseif (!$this->hasPerm('upload', $mandal->id)) {
                $errors[] = "Row {$n}: You do not have upload permission for {$mandal->name} mandal.";
            }

            if (!$village || ($mandal && (int) $village->mandal_id !== (int) $mandal->id)) {
                $errors[] = "Row {$n}: Please select a valid village of the selected mandal.";
            }

            if (!$moduleId) {
                $errors[] = "Row {$n}: Please select a module.";
            } elseif (!$modules->has($moduleId)) {
                $errors[] = "Row {$n}: You do not have permission to upload for the selected module.";
            }

            if ($appNo === '' || mb_strlen($appNo) > 100) {
                $errors[] = "Row {$n}: Application number is required (max 100 characters).";
            } else {
                $combo = $this->comboKey($mandalId, $villageId, $moduleId, $appNo);
                if (isset($seenCombo[$combo])) {
                    $errors[] = "Row {$n}: Same mandal, village, module and application number as row {$seenCombo[$combo]}.";
                } else {
                    $seenCombo[$combo] = $n;
                }
            }

            $prefix = ($mandal && $village) ? $this->keyPrefix($mandal, $village) : null;
            if ($key === '' || isset($seenKeys[$key]) || !$prefix || !$this->isValidKey($key, $prefix)) {
                $errors[] = "Row {$n}: Uploaded PDF could not be verified. Please re-select the file.";
            }
            $seenKeys[$key] = true;

            $cleaned[] = [
                'row'                => $n,
                'mandal_id'          => $mandalId,
                'village_id'         => $villageId,
                'module_id'          => $moduleId ?: null,
                'module'             => $modules->get($moduleId),
                'application_number' => $appNo,
                'file_path'          => $key,
                'file_name'          => Str::limit((string) ($row['fileName'] ?? basename($key)), 250, ''),
                'file_size'          => (int) ($row['fileSize'] ?? 0) ?: null,
            ];
        }

        if (empty($errors)) {
            $errors = $this->duplicateErrors($cleaned);

            if (BhuBharathi::withTrashed()->whereIn('file_path', array_column($cleaned, 'file_path'))->exists()) {
                $errors[] = 'One of the uploaded files is already linked to another record. Please re-select the file.';
            }
        }

        if (!empty($errors)) {
            return response()->json(['success' => false, 'message' => 'Please fix the errors below.', 'errors' => $errors], 422);
        }

        DB::beginTransaction();
        try {
            // Lock the mandal rows so two people submitting the same combination at the
            // same moment are processed one after the other, then re-check duplicates.
            Mandal::whereIn('id', array_unique(array_column($cleaned, 'mandal_id')))->lockForUpdate()->get(['id']);

            if ($dupes = $this->duplicateErrors($cleaned)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Please fix the errors below.', 'errors' => $dupes], 422);
            }

            foreach ($cleaned as $row) {
                unset($row['row']);
                BhuBharathi::create($row + [
                    'file_mime'   => self::MIME,
                    'disk'        => self::DISK,
                    'uploaded_by' => Auth::id(),
                    'uploaded_ip' => $request->ip(),
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Bhu Bharathi store failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Submission failed. Please try again.'], 500);
        }

        $villageCount = count(array_unique(array_column($cleaned, 'village_id')));
        $mandalCount  = count(array_unique(array_column($cleaned, 'mandal_id')));

        return response()->json([
            'success' => true,
            'message' => count($cleaned) . " Bhu Bharathi disposal(s) saved across {$villageCount} village(s) in {$mandalCount} mandal(s).",
        ]);
    }

    // ── CHECK: is this mandal + village + module + application no. already uploaded? ──
    /**
     * Used by the upload page to disable the PDF upload for a row that already exists.
     * GET ?mandal_id=&village_id=&module_id=&application_number=
     */
    public function check(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mandal_id'          => ['required', 'integer'],
            'village_id'         => ['required', 'integer'],
            'module_id'          => ['required', 'integer'],
            'application_number' => ['required', 'string', 'max:100'],
        ]);

        if (!$this->hasPerm('upload', (int) $data['mandal_id'])) {
            return response()->json(['success' => false, 'message' => 'You do not have upload permission for this mandal.'], 403);
        }

        $record = BhuBharathi::with(['uploader:id,name', 'mandal:id,name,slug', 'village:id,name,slug', 'moduleMaster:id,name'])
            ->where('mandal_id', (int) $data['mandal_id'])
            ->where('village_id', (int) $data['village_id'])
            ->where('module_id', (int) $data['module_id'])
            ->where('application_number', trim($data['application_number']))
            ->first();

        return response()->json([
            'success' => true,
            'exists'  => (bool) $record,
            'record'  => $record ? $this->present($record) : null,
        ]);
    }

    // ── UPDATE (single disposal: module / application no / replace PDF) ──────
    /**
     * JSON payload:
     * { module_id, application_number, r2Key?, fileName?, fileSize? }
     * r2Key is only sent when the PDF is being replaced.
     */
    public function update(Request $request, BhuBharathi $bhuBharathi): JsonResponse
    {
        if (!$this->canEditRecord($bhuBharathi)) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to edit this disposal.'], 403);
        }

        $data = $request->validate([
            // Must be a module this user may upload (active) — or the module the record already has
            'module_id'          => [
                'required', 'integer',
                function ($attribute, $value, $fail) use ($bhuBharathi) {
                    $value = (int) $value;
                    if ($value === (int) $bhuBharathi->module_id) {
                        return; // keeping the current module is always fine
                    }
                    if (!$this->allowedModules()->has($value)) {
                        $fail('You do not have permission to use the selected module.');
                    }
                },
            ],
            'application_number' => [
                'required', 'string', 'max:100',
                // unique per mandal + village + module (record's own mandal/village, chosen module)
                Rule::unique('bhu_bharathis', 'application_number')
                    ->ignore($bhuBharathi->id)
                    ->where(fn ($q) => $q
                        ->where('mandal_id', $bhuBharathi->mandal_id)
                        ->where('village_id', $bhuBharathi->village_id)
                        ->where('module_id', (int) $request->input('module_id'))
                        ->whereNull('deleted_at')),
            ],
            'r2Key'    => ['nullable', 'string', 'max:500'],
            'fileName' => ['required_with:r2Key', 'nullable', 'string', 'max:255'],
            'fileSize' => ['required_with:r2Key', 'nullable', 'integer', 'min:1'],
        ], [
            'application_number.unique' => 'This application number already exists for this mandal, village and module.',
            'module_id.required'        => 'Please select a module.',
        ]);

        $bhuBharathi->loadMissing('mandal', 'village');
        $newKey  = $data['r2Key'] ?? null;
        $oldPath = $bhuBharathi->file_path;

        if ($newKey) {
            $prefix = $this->keyPrefix($bhuBharathi->mandal, $bhuBharathi->village);
            if (!$this->isValidKey($newKey, $prefix)
                || BhuBharathi::withTrashed()->where('file_path', $newKey)->whereKeyNot($bhuBharathi->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Uploaded PDF could not be verified. Please re-select the file.'], 422);
            }
        }

        $attrs = [
            'module_id'          => (int) $data['module_id'],
            'module'             => Module::whereKey($data['module_id'])->value('name'),
            'application_number' => trim($data['application_number']),
        ];

        if ($newKey) {
            $attrs += [
                'file_path'   => $newKey,
                'file_name'   => $data['fileName'],
                'file_size'   => $data['fileSize'],
                'file_mime'   => self::MIME,
                'disk'        => self::DISK,
                'uploaded_by' => Auth::id(),
                'uploaded_ip' => $request->ip(),
            ];
        }

        DB::beginTransaction();
        try {
            $bhuBharathi->update($attrs);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Bhu Bharathi update failed: ' . $e->getMessage(), ['id' => $bhuBharathi->id]);
            return response()->json(['success' => false, 'message' => 'Update failed. Please try again.'], 500);
        }

        // Delete the replaced file only after the DB change is committed
        if ($newKey && $oldPath && $oldPath !== $newKey) {
            try {
                Storage::disk(self::DISK)->delete($oldPath);
            } catch (\Throwable $e) {
                Log::warning('Bhu Bharathi: failed to delete old R2 file: ' . $e->getMessage(), ['path' => $oldPath]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Disposal updated successfully.',
            'record'  => $this->present($bhuBharathi->fresh(['uploader:id,name', 'mandal:id,name,slug', 'village:id,name,slug', 'moduleMaster:id,name'])),
        ]);
    }

    // ── OPEN PDF ─────────────────────────────────────────────────────────────
    /**
     * Old "open PDF" link. Never hands out the raw R2 URL any more —
     * it simply sends the user to the secure in-app viewer.
     */
    public function showFile(BhuBharathi $bhuBharathi)
    {
        return redirect()->route('bhu-bharathi.view-pdf', $bhuBharathi);
    }

    // ── SECURE PDF VIEWER (same protections as Pahani viewPdfPage) ───────────
    public function viewPdfPage(BhuBharathi $bhuBharathi)
    {
        // ── Check 1: Document has file path ──
        abort_if(!$bhuBharathi->file_path, 404, 'No file uploaded for this record.');

        // ── Check 2: User has Bhu Bharathi VIEW permission for this mandal ──
        if (!$this->canViewRecord($bhuBharathi)) {
            Log::warning('Bhu Bharathi PDF view denied', [
                'user_id'         => Auth::id(),
                'bhu_bharathi_id' => $bhuBharathi->id,
                'ip'              => request()->ip(),
            ]);
            abort(403, 'You do not have permission to view this file.');
        }

        return $this->securePdfView(
            $bhuBharathi,
            $this->safeBackUrl(route('bhu-bharathi.my-files')),
            'user'
        );
    }

    // ── DIRECT-TO-R2 UPLOAD: single PUT ──────────────────────────────────────
    public function presign(Request $request): JsonResponse
    {
        $request->validate([
            'mandal'  => ['required', 'string', 'exists:mandals,slug'],
            'village' => ['required', 'string'],
            'fileExt' => ['required', 'string', 'in:pdf'],
        ]);

        [$mandal, $village] = $this->resolveLocation($request->mandal, $request->village);
        abort_unless($this->canWriteMandal($mandal->id), 403, 'You do not have permission to upload in this mandal.');

        $key    = $this->newKey($mandal, $village);
        $signed = Storage::disk(self::DISK)->temporaryUploadUrl($key, now()->addMinutes(20), ['ContentType' => self::MIME]);

        return response()->json([
            'success' => true,
            'key'     => $key,
            'url'     => $signed['url'],
            'headers' => $signed['headers'] ?? ['Content-Type' => self::MIME],
        ]);
    }

    // ── DIRECT-TO-R2 UPLOAD: multipart (large files) ─────────────────────────
    public function multipartInit(Request $request): JsonResponse
    {
        $request->validate([
            'mandal'  => ['required', 'string', 'exists:mandals,slug'],
            'village' => ['required', 'string'],
            'fileExt' => ['required', 'string', 'in:pdf'],
        ]);

        [$mandal, $village] = $this->resolveLocation($request->mandal, $request->village);
        abort_unless($this->canWriteMandal($mandal->id), 403, 'You do not have permission to upload in this mandal.');

        $key    = $this->newKey($mandal, $village);
        $result = $this->r2Client()->createMultipartUpload([
            'Bucket'      => config('filesystems.disks.r2.bucket'),
            'Key'         => $key,
            'ContentType' => self::MIME,
        ]);

        return response()->json(['success' => true, 'key' => $key, 'uploadId' => $result['UploadId']]);
    }

    public function multipartSignPart(Request $request): JsonResponse
    {
        $request->validate([
            'key'        => ['required', 'string'],
            'uploadId'   => ['required', 'string'],
            'partNumber' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);
        $this->authorizeKey($request->key);

        $client = $this->r2Client();
        $cmd = $client->getCommand('UploadPart', [
            'Bucket'     => config('filesystems.disks.r2.bucket'),
            'Key'        => $request->key,
            'UploadId'   => $request->uploadId,
            'PartNumber' => (int) $request->partNumber,
        ]);

        return response()->json([
            'success' => true,
            'url'     => (string) $client->createPresignedRequest($cmd, '+20 minutes')->getUri(),
        ]);
    }

    public function multipartComplete(Request $request): JsonResponse
    {
        $request->validate([
            'key'                => ['required', 'string'],
            'uploadId'           => ['required', 'string'],
            'parts'              => ['required', 'array', 'min:1'],
            'parts.*.PartNumber' => ['required', 'integer'],
            'parts.*.ETag'       => ['required', 'string'],
        ]);
        $this->authorizeKey($request->key);

        $this->r2Client()->completeMultipartUpload([
            'Bucket'          => config('filesystems.disks.r2.bucket'),
            'Key'             => $request->key,
            'UploadId'        => $request->uploadId,
            'MultipartUpload' => ['Parts' => $request->parts],
        ]);

        return response()->json(['success' => true, 'key' => $request->key]);
    }

    public function multipartAbort(Request $request): JsonResponse
    {
        $request->validate(['key' => ['required', 'string'], 'uploadId' => ['required', 'string']]);
        $this->authorizeKey($request->key);

        try {
            $this->r2Client()->abortMultipartUpload([
                'Bucket'   => config('filesystems.disks.r2.bucket'),
                'Key'      => $request->key,
                'UploadId' => $request->uploadId,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Bhu Bharathi multipart abort failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    //  HELPERS
    // ═════════════════════════════════════════════════════════════════════════

    /** @return array{upload:int[], view:int[], edit:int[]} */
    private function permissions(): array
    {
        if ($this->permCache !== null) {
            return $this->permCache;
        }

        /** @var \App\Models\UserDocumentPermission|null $p */
        $p = Auth::user()?->documentPermission;

        return $this->permCache = [
            'upload' => $p?->getBbUploadMandalIds() ?? [],
            'view'   => $p?->getBbViewMandalIds() ?? [],
            'edit'   => $p?->getBbEditMandalIds() ?? [],
        ];
    }

    private function hasPerm(string $type, int $mandalId): bool
    {
        return in_array($mandalId, $this->permissions()[$type], true);
    }

    private function canViewMandal(int $mandalId): bool
    {
        return $this->hasPerm('view', $mandalId) || $this->hasPerm('edit', $mandalId);
    }

    private function canWriteMandal(int $mandalId): bool
    {
        return $this->hasPerm('upload', $mandalId) || $this->hasPerm('edit', $mandalId);
    }

    /** View (open PDF) — only when admin gave Bhu Bharathi VIEW permission for the mandal. */
    private function canViewRecord(BhuBharathi $r): bool
    {
        return $this->hasPerm('view', (int) $r->mandal_id);
    }

    /** Edit — only when admin gave Bhu Bharathi EDIT permission for the mandal. */
    private function canEditRecord(BhuBharathi $r): bool
    {
        return $this->hasPerm('edit', (int) $r->mandal_id);
    }

    /** Module IDs the admin allowed this user to upload (bb_upload_module_ids). */
    private function uploadModuleIds(): array
    {
        if ($this->moduleCache !== null) {
            return $this->moduleCache;
        }

        /** @var \App\Models\UserDocumentPermission|null $p */
        $p = Auth::user()?->documentPermission;

        return $this->moduleCache = $p?->getBbUploadModuleIds() ?? [];
    }

    /** Active modules this user may upload, as [id => name]. */
    private function allowedModules()
    {
        $ids = $this->uploadModuleIds();

        return empty($ids)
            ? collect()
            : Module::active()->whereIn('id', $ids)->orderBy('name')->pluck('name', 'id');
    }

    /** Case-insensitive key for the mandal + village + module + application number combination. */
    private function comboKey(int $mandalId, int $villageId, int $moduleId, string $appNo): string
    {
        return "{$mandalId}|{$villageId}|{$moduleId}|" . mb_strtolower(trim($appNo));
    }

    /**
     * "Row N: ... already uploaded" for every row whose combination is already saved.
     * Soft-deleted records are ignored (SoftDeletes global scope).
     */
    private function duplicateErrors(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $existing = BhuBharathi::where(function ($q) use ($rows) {
                foreach ($rows as $r) {
                    $q->orWhere(fn ($qq) => $qq
                        ->where('mandal_id', $r['mandal_id'])
                        ->where('village_id', $r['village_id'])
                        ->where('module_id', $r['module_id'])
                        ->where('application_number', $r['application_number']));
                }
            })
            ->get(['mandal_id', 'village_id', 'module_id', 'application_number'])
            ->mapWithKeys(fn ($e) => [$this->comboKey((int) $e->mandal_id, (int) $e->village_id, (int) $e->module_id, $e->application_number) => true]);

        $errors = [];
        foreach ($rows as $r) {
            if ($existing->has($this->comboKey((int) $r['mandal_id'], (int) $r['village_id'], (int) $r['module_id'], $r['application_number']))) {
                $errors[] = "Row {$r['row']}: Application number {$r['application_number']} is already uploaded for this mandal, village and module.";
            }
        }

        return $errors;
    }

    /**
     * Shared by the user and admin viewers:
     *  Check 3 — file exists in R2,
     *  Check 4 — short-lived signed URL (PDF.js loads it straight from R2),
     *  audit log + no-cache / no-frame response headers.
     */
    private function securePdfView(BhuBharathi $bb, string $backUrl, string $context)
    {
        $disk = $bb->disk ?: self::DISK;

        // ── Check 3: File exists in R2 ──
        if (!Storage::disk($disk)->exists($bb->file_path)) {
            Log::error('Bhu Bharathi PDF not found in R2', [
                'bhu_bharathi_id' => $bb->id,
                'file_path'       => $bb->file_path,
            ]);
            abort(404, 'File not found in storage.');
        }

        // ── Check 4: Generate secure signed URL ──
        $minutes = (int) config('filesystems.disks.r2.signed_url_expires', 60);
        try {
            $signedUrl = Storage::disk($disk)->temporaryUrl(
                $bb->file_path,
                Carbon::now()->addMinutes($minutes),
                ['ResponseContentType' => self::MIME]
            );
        } catch (\Throwable $e) {
            Log::error('Bhu Bharathi: failed to generate signed URL', [
                'user_id'         => Auth::id(),
                'bhu_bharathi_id' => $bb->id,
                'error'           => $e->getMessage(),
            ]);
            abort(500, 'Unable to generate secure URL');
        }

        $bb->loadMissing(['mandal:id,name', 'village:id,name', 'moduleMaster:id,name']);
        $user = Auth::user();

        // ── Audit trail ──
        Log::info('Bhu Bharathi PDF viewed', [
            'context'            => $context,
            'user_id'            => $user?->id,
            'user_email'         => $user?->email,
            'bhu_bharathi_id'    => $bb->id,
            'application_number' => $bb->application_number,
            'ip'                 => request()->ip(),
            'user_agent'         => substr((string) request()->userAgent(), 0, 255),
        ]);

        return response()
            ->view('bhu-bharathi.pdf-viewer', [
                'record'         => $bb,
                'pdfSourceUrl'   => $signedUrl,
                'backUrl'        => $backUrl,
                'expiresMinutes' => $minutes,
                // Visible watermark on every page: who viewed it, from where, when
                'watermark'      => trim(($user?->name ?? 'User') . ' · ' . ($user?->email ?? '') . ' · ' . request()->ip()
                                    . ' · ' . now()->format('d-M-Y h:i A')),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-Frame-Options', 'DENY')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Referrer-Policy', 'no-referrer');
    }

    /** Previous page if it is on this site (and not the viewer itself), otherwise $fallback. */
    private function safeBackUrl(string $fallback): string
    {
        $prev = url()->previous();
        $sameHost = parse_url($prev, PHP_URL_HOST) === request()->getHost();

        return ($sameHost && $prev !== url()->current() && !str_contains($prev, '/view-pdf'))
            ? $prev
            : $fallback;
    }

    private function resolveLocation(string $mandalSlug, string $villageSlug): array
    {
        $mandal = Mandal::where('slug', $mandalSlug)->where('is_active', true)->firstOrFail();
        $village = Village::where('mandal_id', $mandal->id)
            ->where('slug', $villageSlug)
            ->where('is_active', true)
            ->firstOrFail();

        return [$mandal, $village];
    }

    private function keyPrefix(Mandal $mandal, Village $village): string
    {
        return self::KEY_ROOT . "/{$mandal->slug}/{$village->slug}/";
    }

    private function newKey(Mandal $mandal, Village $village): string
    {
        return $this->keyPrefix($mandal, $village) . Str::uuid() . '.pdf';
    }

    /** Guards against forged keys: must sit under this mandal/village, be a PDF and exist in R2. */
    private function isValidKey(string $key, string $prefix): bool
    {
        return Str::startsWith($key, $prefix)
            && !Str::contains($key, '..')
            && strtolower(pathinfo($key, PATHINFO_EXTENSION)) === 'pdf'
            && Storage::disk(self::DISK)->exists($key);
    }

    /** For multipart calls that only carry the key: check root folder + mandal permission. */
    private function authorizeKey(string $key): void
    {
        $parts = explode('/', $key);

        abort_unless(
            count($parts) === 4 && $parts[0] === self::KEY_ROOT && Str::endsWith(strtolower($key), '.pdf'),
            422,
            'Invalid upload key.'
        );

        $mandal = Mandal::where('slug', $parts[1])->first();
        abort_unless($mandal && $this->canWriteMandal($mandal->id), 403, 'You do not have permission to upload in this mandal.');
    }

    private function present(BhuBharathi $r): array
    {
        return [
            'id'                 => $r->id,
            'module_id'          => $r->module_id,
            'module'             => $r->module,
            'module_name'        => $r->moduleMaster?->name ?? $r->module,
            'mandal_name'        => $r->mandal?->name,
            'mandal_slug'        => $r->mandal?->slug,
            'village_name'       => $r->village?->name,
            'village_slug'       => $r->village?->slug,
            'application_number' => $r->application_number,
            'file_name'          => $r->file_name,
            'file_size_human'    => $r->file_size_human,
            'has_file'           => (bool) $r->file_path,
            // URL only sent when the user may open it
            'file_url'           => ($r->file_path && $this->canViewRecord($r)) ? route('bhu-bharathi.view-pdf', $r) : null,
            'uploaded_by'        => $r->uploaded_by,
            'uploader_name'      => $r->uploader?->name,
            'created_at'         => $r->created_at?->format('d-M-Y h:i A'),
            'updated_at'         => $r->updated_at?->format('d-M-Y h:i A'),
            'can_view'           => $this->canViewRecord($r),
            'can_edit'           => $this->canEditRecord($r),
        ];
    }

    private function r2Client(): S3Client
    {
        $c = config('filesystems.disks.r2');

        return new S3Client([
            'version'     => 'latest',
            'region'      => $c['region'] ?? 'auto',
            'endpoint'    => $c['endpoint'],
            'credentials' => ['key' => $c['key'], 'secret' => $c['secret']],
            'use_path_style_endpoint' => $c['use_path_style_endpoint'] ?? false,
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    //  ADMIN — list of all disposals + open any PDF (routes in the is_admin group)
    // ═════════════════════════════════════════════════════════════════════════

    public function adminIndex(Request $request)
    {
        $records = BhuBharathi::query()
            ->with([
                'uploader:id,name,email',
                'mandal:id,name',
                'village:id,name',
                'moduleMaster:id,name',
            ])
            ->when($request->filled('search_user'), function ($q) use ($request) {
                $q->whereHas('uploader', fn ($u) => $u->where('name', 'like', '%' . $request->search_user . '%'));
            })
            ->when($request->filled('mandal_id'), fn ($q) => $q->where('mandal_id', (int) $request->mandal_id))
            ->when($request->filled('village_id'), fn ($q) => $q->where('village_id', (int) $request->village_id))
            ->when($request->filled('module_id'), fn ($q) => $q->where('module_id', (int) $request->module_id))
            ->when($request->filled('application_number'), function ($q) use ($request) {
                $q->where('application_number', 'like', '%' . trim($request->application_number) . '%');
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();
 
        $mandals  = Mandal::orderBy('name')->get(['id', 'name']);
        $villages = Village::orderBy('name')->get(['id', 'name', 'mandal_id']);
        $modules  = Module::orderBy('name')->get(['id', 'name', 'is_active']);
 
        return view('admin.bhu-bharathi-management.index', compact('records', 'mandals', 'villages', 'modules'));
    }
 
    /** Open the PDF in the secure viewer (admin may view every disposal; route is in the is_admin group). */
    public function file(BhuBharathi $bhuBharathi)
    {
        abort_if(!$bhuBharathi->file_path, 404, 'No file uploaded for this record.');

        return $this->securePdfView(
            $bhuBharathi,
            $this->safeBackUrl(route('admin.bhu-bharathi-management.index')),
            'admin'
        );
    }
}