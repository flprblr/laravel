<?php

namespace App\Http\Controllers\Maintainers;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Maintainers\Users\StoreUserRequest;
use App\Http\Requests\Maintainers\Users\UpdateUserRequest;
use App\Imports\UsersImport;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $allowedSortColumns = ['id', 'name', 'email', 'created_at', 'updated_at'];
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

        $query = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate($perPage)->withQueryString();

        return Inertia::render('maintainers/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'sort_by', 'sort_direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::select(['id', 'name'])->orderBy('id', 'asc')->get();

        return Inertia::render('maintainers/users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:users,name'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:12',
                'max:128',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[^\w\s]).{12,128}$/',
            ],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ], [
            'password.regex' => 'La contraseña debe tener entre 12 y 128 caracteres, incluir letras, números y símbolos.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()
            ->route('maintainers.users.create')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles.permissions' => function ($query) {
            $query->orderBy('id', 'asc');
        }]);

        return Inertia::render('maintainers/users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load(['roles.permissions' => function ($query) {
            $query->orderBy('id', 'asc');
        }]);
        $roles = Role::select(['id', 'name'])->orderBy('id', 'asc')->get();

        return Inertia::render('maintainers/users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:users,name,'.$user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => [
                'required',
                'string',
                'min:12',
                'max:128',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[^\w\s]).{12,128}$/',
            ],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ], [
            'password.regex' => 'La contraseña debe tener entre 12 y 128 caracteres, incluir letras, números y símbolos.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        } else {
            $user->syncRoles([]);
        }

        return redirect()
            ->route('maintainers.users.edit', $user)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Export to Excel
     */
    public function export()
    {
        $app = config('app.name');
        $time = Carbon::now('America/Santiago')->format('d-m-Y H-i-s');

        $filename = "{$app} - Users {$time}.xlsx";

        return Excel::download(new UsersExport, $filename);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return Inertia::render('maintainers/users/Import');
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
            Excel::import(new UsersImport, $request->file('file'));

            return redirect()
                ->route('maintainers.users.import.form')
                ->with('success', 'Users imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
