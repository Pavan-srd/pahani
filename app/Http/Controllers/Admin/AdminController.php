<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mandal;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkingOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard shell (sidebar + tabs).
     * Table data itself is loaded client-side via the JSON endpoints below.
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Show the logged-in admin's profile page.
     */
    public function profile()
    {
        return view('admin.profile', [
            'user' => auth()->user(),
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
       JSON LIST ENDPOINTS — one per sidebar tab
    ══════════════════════════════════════════════════════════════ */

    /**
     * GET /api/admin/mandals
     */
    public function mandals(Request $request)
    {
        $mandals = Mandal::withCount('villages')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->get()
            ->map(fn (Mandal $m) => [
                'id'             => $m->id,
                'name'           => $m->name,
                'slug'           => $m->slug,
                'villages_count' => $m->villages_count,
                'is_active'      => (bool) $m->is_active,
            ]);

        return response()->json($mandals);
    }

    /**
     * GET /api/admin/villages
     */
    public function villages(Request $request)
    {
        $villages = Village::with('mandal')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('mandal_id'), function ($q) use ($request) {
                $q->where('mandal_id', $request->mandal_id);
            })
            ->orderBy('name')
            ->get()
            ->map(fn (Village $v) => [
                'id'          => $v->id,
                'name'        => $v->name,
                'slug'        => $v->slug,
                'mandal_id'   => $v->mandal_id,
                'mandal_name' => $v->mandal->name ?? null,
                'is_active'   => (bool) $v->is_active,
            ]);

        return response()->json($villages);
    }

    /**
     * GET /api/admin/users
     * Returns all non-admin users with their working office and status
     * UPDATED: Now includes working_office_id, working_office_name, and status
     */
    public function users(Request $request)
    {
        $users = User::with('workingOffice')
            ->where('is_admin', false)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => [
                'id'                   => $u->id,
                'name'                 => $u->name,
                'email'                => $u->email,
                'role'                 => $u->role ?? 'User',
                'working_office_id'    => $u->working_office_id,
                'working_office_name'  => $u->workingOffice?->name ?? 'Not Assigned',
                'status'               => (int) $u->status,
                'mandal'               => $u->getMandal?->name,
                'mandal_ids'           => $u->mandals?->pluck('id')->toArray() ?? [],
            ]);

        return response()->json($users);
    }

    /* ══════════════════════════════════════════════════════════════
       STORE — Add Mandal / Add Village (called from dashboard modals)
    ══════════════════════════════════════════════════════════════ */

    /**
     * POST /api/admin/mandals
     * Body: { name: string }
     */
    public function storeMandal(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:mandals,name'],
        ], [
            'name.unique' => 'A mandal with this name already exists.',
        ]);

        $mandal = Mandal::create([
            'name'      => $validated['name'],
            'slug'      => Str::slug($validated['name']),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mandal added successfully.',
            'mandal'  => [
                'id'             => $mandal->id,
                'name'           => $mandal->name,
                'slug'           => $mandal->slug,
                'villages_count' => 0,
                'is_active'      => (bool) $mandal->is_active,
            ],
        ], 201);
    }

    /**
     * POST /api/admin/villages
     * Body: { mandal_id: int, name: string }
     */
    public function storeVillage(Request $request)
    {
        $validated = $request->validate([
            'mandal_id' => ['required', 'integer', 'exists:mandals,id'],
            'name'      => [
                'required',
                'string',
                'max:255',
                Rule::unique('villages', 'name')->where(
                    fn ($query) => $query->where('mandal_id', $request->input('mandal_id'))
                ),
            ],
        ], [
            'name.unique' => 'A village with this name already exists under the selected mandal.',
        ]);

        $village = Village::create([
            'mandal_id' => $validated['mandal_id'],
            'name'      => $validated['name'],
            'slug'      => Str::slug($validated['name']),
            'is_active' => true,
        ]);

        $village->load('mandal');

        return response()->json([
            'success' => true,
            'message' => 'Village added successfully.',
            'village' => [
                'id'          => $village->id,
                'name'        => $village->name,
                'slug'        => $village->slug,
                'mandal_id'   => $village->mandal_id,
                'mandal_name' => $village->mandal->name ?? null,
                'is_active'   => (bool) $village->is_active,
            ],
        ], 201);
    }

    /* ══════════════════════════════════════════════════════════════
       EDIT (prefill) — return a single record for the edit modal
    ══════════════════════════════════════════════════════════════ */

    /**
     * GET /api/admin/mandals/{mandal}/edit
     */
    public function editMandal(Mandal $mandal)
    {
        return response()->json([
            'id'   => $mandal->id,
            'name' => $mandal->name,
            'slug' => $mandal->slug,
        ]);
    }

    /**
     * GET /api/admin/villages/{village}/edit
     */
    public function editVillage(Village $village)
    {
        return response()->json([
            'id'        => $village->id,
            'name'      => $village->name,
            'slug'      => $village->slug,
            'mandal_id' => $village->mandal_id,
        ]);
    }

    /**
     * GET /api/admin/users/{user}/edit
     * Returns user data along with all available mandals, working offices, and current assignments
     * UPDATED: Now includes working_office_id, status, and list of all working offices
     */
    public function editUser(User $user)
    {
        $allMandals = Mandal::orderBy('name')->get(['id', 'name']);
        $userMandalIds = $user->mandals()->pluck('mandal_id')->toArray();
        
        // Get all working offices for dropdown
        $allWorkingOffices = WorkingOffice::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'id'                    => $user->id,
            'name'                  => $user->name,
            'email'                 => $user->email,
            'role'                  => $user->role ?? 'User',
            'working_office_id'     => $user->working_office_id,
            'status'                => (int) $user->status,
            'working_offices'       => $allWorkingOffices->map(fn (WorkingOffice $wo) => [
                'id'   => $wo->id,
                'name' => $wo->name,
            ]),
            'mandals'               => $allMandals->map(fn (Mandal $m) => [
                'id'       => $m->id,
                'name'     => $m->name,
                'assigned' => in_array($m->id, $userMandalIds),
            ]),
            'assigned_mandal_ids'   => $userMandalIds,
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
       UPDATE — one per tab
    ══════════════════════════════════════════════════════════════ */

    /**
     * PUT /api/admin/mandals/{mandal}
     * Body: { name: string }
     */
    public function updateMandal(Request $request, Mandal $mandal)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('mandals', 'name')->ignore($mandal->id),
            ],
        ], [
            'name.unique' => 'A mandal with this name already exists.',
        ]);

        $mandal->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mandal updated successfully.',
            'mandal'  => [
                'id'             => $mandal->id,
                'name'           => $mandal->name,
                'slug'           => $mandal->slug,
                'villages_count' => $mandal->villages()->count(),
                'is_active'      => (bool) $mandal->is_active,
            ],
        ]);
    }

    /**
     * PUT /api/admin/villages/{village}
     * Body: { mandal_id: int, name: string }
     */
    public function updateVillage(Request $request, Village $village)
    {
        $validated = $request->validate([
            'mandal_id' => ['required', 'integer', 'exists:mandals,id'],
            'name'      => [
                'required',
                'string',
                'max:255',
                Rule::unique('villages', 'name')
                    ->where(fn ($query) => $query->where('mandal_id', $request->input('mandal_id')))
                    ->ignore($village->id),
            ],
        ], [
            'name.unique' => 'A village with this name already exists under the selected mandal.',
        ]);

        $village->update([
            'mandal_id' => $validated['mandal_id'],
            'name'      => $validated['name'],
            'slug'      => Str::slug($validated['name']),
        ]);

        $village->load('mandal');

        return response()->json([
            'success' => true,
            'message' => 'Village updated successfully.',
            'village' => [
                'id'          => $village->id,
                'name'        => $village->name,
                'slug'        => $village->slug,
                'mandal_id'   => $village->mandal_id,
                'mandal_name' => $village->mandal->name ?? null,
                'is_active'   => (bool) $village->is_active,
            ],
        ]);
    }

    /**
     * PUT /api/admin/users/{user}
     * Body: { name: string, email: string, password?: string, working_office_id: int, status: int, mandal_ids?: array }
     * UPDATED: Now accepts and validates working_office_id and status
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'email'               => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password'            => ['nullable', 'string', 'min:8', 'confirmed'],
            'working_office_id'   => ['required', 'integer', 'exists:working_offices,id'],
            'status'              => ['nullable', 'integer', 'in:0,1'],
            'mandal_ids'          => ['nullable', 'array'],
            'mandal_ids.*'        => ['integer', 'exists:mandals,id'],
        ], [
            'email.unique'              => 'A user with this email already exists.',
            'working_office_id.required' => 'Working office is required.',
            'working_office_id.exists'   => 'The selected working office does not exist.',
            'status.in'                 => 'Status must be 0 (Inactive) or 1 (Active).',
        ]);

        $user->name              = $validated['name'];
        $user->email             = $validated['email'];
        $user->working_office_id = $validated['working_office_id'];
        $user->status            = $validated['status'];
        
        if (! empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();

        // Sync mandal assignments (use sync to replace all assignments)
        $mandalIds = $validated['mandal_ids'] ?? [];
        $user->mandals()->sync($mandalIds);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'user'    => [
                'id'                  => $user->id,
                'name'                => $user->name,
                'email'               => $user->email,
                'role'                => $user->role ?? 'User',
                'working_office_id'   => $user->working_office_id,
                'status'              => (int) $user->status,
                'is_active'           => (bool) ($user->is_active ?? true),
            ],
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
       TOGGLE STATUS — reused by all three tabs
    ══════════════════════════════════════════════════════════════ */

    public function toggleMandalStatus(Mandal $mandal)
    {
        $mandal->update(['is_active' => ! $mandal->is_active]);

        return response()->json(['success' => true, 'is_active' => $mandal->is_active]);
    }

    public function toggleVillageStatus(Village $village)
    {
        $village->update(['is_active' => ! $village->is_active]);

        return response()->json(['success' => true, 'is_active' => $village->is_active]);
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => ! $user->is_active]);

        return response()->json(['success' => true, 'is_active' => $user->is_active]);
    }

    /* ══════════════════════════════════════════════════════════════
       DELETE — one per tab
    ══════════════════════════════════════════════════════════════ */

    public function destroyMandal(Mandal $mandal)
    {
        if ($mandal->villages()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a Mandal that still has villages under it.',
            ], 422);
        }

        $mandal->delete();

        return response()->json(['success' => true, 'message' => 'Mandal deleted successfully.']);
    }

    public function destroyVillage(Village $village)
    {
        $village->delete();

        return response()->json(['success' => true, 'message' => 'Village deleted successfully.']);
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }

    /* ══════════════════════════════════════════════════════════════
       WORKING OFFICE — CRUD operations
    ══════════════════════════════════════════════════════════════ */

    public function workingOffices()
    {
        return response()->json(WorkingOffice::all());
    }

    public function storeWorkingOffice(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:working_offices,name',
        ]);
        $office = WorkingOffice::create($validated);
        return response()->json(['success' => true, 'message' => 'Working office created successfully', 'data' => $office], 201);
    }

    public function editWorkingOffice($id)
    {
        $office = WorkingOffice::findOrFail($id);
        return response()->json($office);
    }

    public function updateWorkingOffice(Request $request, $id)
    {
        $office = WorkingOffice::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:working_offices,name,' . $id,
        ]);
        $office->update($validated);
        return response()->json(['success' => true, 'message' => 'Working office updated successfully', 'data' => $office]);
    }

    public function destroyWorkingOffice($id)
    {
        $office = WorkingOffice::findOrFail($id);
        $office->delete();
        return response()->json(['success' => true, 'message' => 'Working office deleted successfully']);
    }
}