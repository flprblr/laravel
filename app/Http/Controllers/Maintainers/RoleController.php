<?php

namespace App\Http\Controllers\Maintainers;

use App\Exports\RolesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Maintainers\Roles\StoreRoleRequest;
use App\Http\Requests\Maintainers\Roles\UpdateRoleRequest;
use App\Imports\RolesImport;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $allowedSortColumns = ['id', 'name', 'created_at', 'updated_at'];
        $allowedSortDirections = ['asc', 'desc'];

        $sortBy = $request->input('sort_by', 'name');
        if (! in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'name';
        }

        $sortDirection = $request->input('sort_direction', 'asc');
        if (! in_array($sortDirection, $allowedSortDirections)) {
            $sortDirection = 'asc';
        }

        $perPage = $request->input('per_page', 15);

        $query = Role::select(['id', 'name', 'created_at', 'updated_at']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $query->orderBy($sortBy, $sortDirection);

        $roles = $query->paginate($perPage)->withQueryString();

        return Inertia::render('maintainers/roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search', 'sort_by', 'sort_direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::select(['id', 'name'])->orderBy('id', 'asc')->get();

        return Inertia::render('maintainers/roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()
            ->route('maintainers.roles.create')
            ->with('success', 'Rol creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions' => function ($query) {
            $query->orderBy('id', 'asc');
        }]);

        return Inertia::render('maintainers/roles/Show', [
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::select(['id', 'name'])->orderBy('id', 'asc')->get();

        return Inertia::render('maintainers/roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()
            ->route('maintainers.roles.edit', $role)
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return back()->with('success', 'Rol eliminado correctamente.');
    }

    /**
     * Export to Excel
     */
    public function export()
    {
        $app = config('app.name');
        $time = Carbon::now('America/Santiago')->format('d-m-Y H-i-s');

        $filename = "{$app} - Roles {$time}.xlsx";

        return Excel::download(new RolesExport, $filename);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return Inertia::render('maintainers/roles/Import');
    }

    /**
     * Import from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx|max:2048',
        ]);

        try {
            Excel::import(new RolesImport, $request->file('file'));

            return redirect()
                ->route('maintainers.roles.import.form')
                ->with('success', 'Roles imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
