<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BhuBharathi;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Bhu Bharathi Modules master — same pattern as Working Office:
 *   GET    /admin/modules                → page (view)
 *   GET    /api/admin/modules            → list (JSON)
 *   POST   /api/admin/modules            → store
 *   GET    /api/admin/modules/{module}   → edit (single, JSON)
 *   PUT    /api/admin/modules/{module}   → update
 *   PATCH  /api/admin/modules/{module}/toggle-status
 *   DELETE /api/admin/modules/{module}   → destroy
 */
class ModuleController extends Controller
{
    // ── PAGE ─────────────────────────────────────────────────────────────────
    public function index()
    {
        return view('admin.modules.index');
    }

    // ── LIST ─────────────────────────────────────────────────────────────────
    public function list(Request $request): JsonResponse
    {
        $modules = Module::withCount('bhuBharathis')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->orderBy('name')
            ->get()
            ->map(fn (Module $m) => $this->present($m));

        return response()->json($modules);
    }

    // ── STORE ────────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $request->merge(['name' => $this->cleanName((string) $request->input('name', ''))]);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:150', Rule::unique('modules', 'name')],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Module name is required.',
            'name.unique'   => 'A module with this name already exists.',
        ]);

        $name = $validated['name'];

        $module = Module::create([
            'name'      => $name,
            'slug'      => $this->uniqueSlug($name),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Module added successfully.',
            'module'  => $this->present($module->loadCount('bhuBharathis')),
        ], 201);
    }

    // ── EDIT (single) ────────────────────────────────────────────────────────
    public function edit(Module $module): JsonResponse
    {
        return response()->json([
            'success' => true,
            'module'  => $this->present($module->loadCount('bhuBharathis')),
        ]);
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────
    public function update(Request $request, Module $module): JsonResponse
    {
        $request->merge(['name' => $this->cleanName((string) $request->input('name', ''))]);

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:150', Rule::unique('modules', 'name')->ignore($module->id)],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Module name is required.',
            'name.unique'   => 'A module with this name already exists.',
        ]);

        $name    = $validated['name'];
        $renamed = $name !== $module->name;

        DB::beginTransaction();
        try {
            $module->update([
                'name'      => $name,
                'slug'      => $renamed ? $this->uniqueSlug($name, $module->id) : $module->slug,
                'is_active' => $validated['is_active'] ?? $module->is_active,
            ]);

            // Keep the module name stored on disposals in sync
            if ($renamed) {
                BhuBharathi::withTrashed()->where('module_id', $module->id)->update(['module' => $name]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Module update failed: ' . $e->getMessage(), ['module_id' => $module->id]);
            return response()->json(['success' => false, 'message' => 'Failed to update module.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Module updated successfully.',
            'module'  => $this->present($module->loadCount('bhuBharathis')),
        ]);
    }

    // ── TOGGLE STATUS ────────────────────────────────────────────────────────
    public function toggleStatus(Module $module): JsonResponse
    {
        $module->update(['is_active' => !$module->is_active]);

        return response()->json([
            'success'   => true,
            'message'   => $module->is_active ? 'Module activated.' : 'Module deactivated.',
            'is_active' => $module->is_active,
        ]);
    }

    // ── DELETE ───────────────────────────────────────────────────────────────
    public function destroy(Module $module): JsonResponse
    {
        $used = BhuBharathi::withTrashed()->where('module_id', $module->id)->count();

        if ($used > 0) {
            return response()->json([
                'success' => false,
                'message' => "This module is used by {$used} Bhu Bharathi disposal(s) and cannot be deleted. Mark it Inactive instead.",
            ], 422);
        }

        $module->delete();

        return response()->json(['success' => true, 'message' => 'Module deleted successfully.']);
    }

    // ── HELPERS ──────────────────────────────────────────────────────────────
    private function present(Module $m): array
    {
        return [
            'id'                  => $m->id,
            'name'                => $m->name,
            'slug'                => $m->slug,
            'is_active'           => (bool) $m->is_active,
            'disposals_count'     => (int) ($m->bhu_bharathis_count ?? 0),
            'created_at'          => $m->created_at?->toIso8601String(),
        ];
    }

    private function cleanName(string $name): string
    {
        return preg_replace('/\s+/', ' ', trim($name));
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'module';
        $slug = $base;
        $i    = 2;

        while (Module::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}