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
 *
 * Files go browser → R2 directly (presigned PUT, or multipart for large files),
 * then only a small JSON payload is sent to store()/update().
 */
class BhuBharathiController extends Controller
{
    private const DISK     = 'r2';
    private const KEY_ROOT = 'bhu-bharathi';
    private const MIME     = 'application/pdf';

    private ?array $permCache = null;

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

        // Active modules (admin-managed) for the Module dropdown
        $modules = Module::active()->orderBy('name')->get(['id', 'name']);

        return view('bhu_bharathi', [
            'mandals'     => $mandals,
            'modules'     => $modules,
            'permissions' => $this->permissions(),   // upload / view / edit IDs for the JS
            'user'        => $user,
        ]);
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

    // ── STORE (new disposals, bulk) ──────────────────────────────────────────
    /**
     * JSON payload:
     * {
     *   mandal:  "sangareddy",
     *   village: "kandi",
     *   records: [
     *     { module_id, application_number, r2Key, fileName, fileSize }, ...
     *   ]
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mandal'  => ['required', 'string', 'exists:mandals,slug'],
            'village' => ['required', 'string'],
            'records' => ['required', 'array', 'min:1', 'max:100'],
        ]);

        [$mandal, $village] = $this->resolveLocation($data['mandal'], $data['village']);

        if (!$this->hasPerm('upload', $mandal->id)) {
            return response()->json(['success' => false, 'message' => 'You do not have upload permission for this mandal.'], 403);
        }

        $prefix   = $this->keyPrefix($mandal, $village);
        $modules  = Module::active()->pluck('name', 'id');   // [id => name]
        $errors   = [];
        $cleaned  = [];
        $seenApp  = [];
        $seenKeys = [];

        foreach ($data['records'] as $i => $row) {
            $n      = $i + 1;
            $moduleId = (int) ($row['module_id'] ?? 0);
            $appNo  = trim((string) ($row['application_number'] ?? ''));
            $key    = (string) ($row['r2Key'] ?? '');

            if (!$moduleId || !$modules->has($moduleId)) {
                $errors[] = "Row {$n}: Please select a valid module.";
            }
            if ($appNo === '' || mb_strlen($appNo) > 100) {
                $errors[] = "Row {$n}: Application number is required (max 100 characters).";
            } elseif (isset($seenApp[mb_strtolower($appNo)])) {
                $errors[] = "Row {$n}: Application number {$appNo} is repeated in this submission.";
            }
            if ($key === '' || isset($seenKeys[$key]) || !$this->isValidKey($key, $prefix)) {
                $errors[] = "Row {$n}: Uploaded PDF could not be verified. Please re-select the file.";
            }

            $seenApp[mb_strtolower($appNo)] = true;
            $seenKeys[$key] = true;

            $cleaned[] = [
                'module_id'          => $moduleId ?: null,
                'module'             => $modules->get($moduleId),
                'application_number' => $appNo,
                'file_path'          => $key,
                'file_name'          => Str::limit((string) ($row['fileName'] ?? basename($key)), 250, ''),
                'file_size'          => (int) ($row['fileSize'] ?? 0) ?: null,
            ];
        }

        if (empty($errors)) {
            // Application numbers already registered (soft-deleted rows ignored by the global scope)
            $taken = BhuBharathi::whereIn('application_number', array_column($cleaned, 'application_number'))
                ->pluck('application_number')->all();
            foreach ($taken as $t) {
                $errors[] = "Application number {$t} already exists.";
            }

            if (BhuBharathi::withTrashed()->whereIn('file_path', array_column($cleaned, 'file_path'))->exists()) {
                $errors[] = 'One of the uploaded files is already linked to another record. Please re-select the file.';
            }
        }

        if (!empty($errors)) {
            return response()->json(['success' => false, 'message' => 'Please fix the errors below.', 'errors' => $errors], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($cleaned as $row) {
                BhuBharathi::create($row + [
                    'mandal_id'   => $mandal->id,
                    'village_id'  => $village->id,
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

        return response()->json([
            'success' => true,
            'message' => count($cleaned) . " Bhu Bharathi disposal(s) saved for {$village->name}, {$mandal->name}.",
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
            // Must be an active module — or the module the record already has
            'module_id'          => [
                'required', 'integer',
                Rule::exists('modules', 'id')->where(function ($q) use ($bhuBharathi) {
                    $q->where('is_active', true)
                      ->when($bhuBharathi->module_id, fn ($qq) => $qq->orWhere('id', $bhuBharathi->module_id));
                }),
            ],
            'application_number' => [
                'required', 'string', 'max:100',
                Rule::unique('bhu_bharathis', 'application_number')
                    ->ignore($bhuBharathi->id)
                    ->whereNull('deleted_at'),
            ],
            'r2Key'    => ['nullable', 'string', 'max:500'],
            'fileName' => ['required_with:r2Key', 'nullable', 'string', 'max:255'],
            'fileSize' => ['required_with:r2Key', 'nullable', 'integer', 'min:1'],
        ], [
            'application_number.unique' => 'This application number already exists.',
            'module_id.required'        => 'Please select a module.',
            'module_id.exists'          => 'Please select a valid module.',
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
            'record'  => $this->present($bhuBharathi->fresh('uploader:id,name')),
        ]);
    }

    // ── OPEN PDF ─────────────────────────────────────────────────────────────
    public function showFile(BhuBharathi $bhuBharathi)
    {
        abort_unless($this->canViewRecord($bhuBharathi), 403, 'You do not have permission to view this file.');
        abort_if(!$bhuBharathi->file_path, 404, 'No file uploaded for this record.');

        $disk = $bhuBharathi->disk ?: self::DISK;
        abort_unless(Storage::disk($disk)->exists($bhuBharathi->file_path), 404, 'File not found in storage.');

        $url = Storage::disk($disk)->temporaryUrl(
            $bhuBharathi->file_path,
            Carbon::now()->addMinutes(10),
            [
                'ResponseContentType'        => self::MIME,
                'ResponseContentDisposition' => 'inline',
            ]
        );

        return redirect($url);
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
            'application_number' => $r->application_number,
            'file_name'          => $r->file_name,
            'file_size_human'    => $r->file_size_human,
            'has_file'           => (bool) $r->file_path,
            // URL only sent when the user may open it
            'file_url'           => ($r->file_path && $this->canViewRecord($r)) ? route('bhu-bharathi.file', $r) : null,
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
}