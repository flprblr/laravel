<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test permissions and roles functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Test de Permisos y Roles ===');

        // Verificar permisos
        $this->info("\n1. Verificando permisos:");
        $permissions = Permission::all();
        $this->info("Total de permisos: {$permissions->count()}");

        foreach ($permissions as $permission) {
            $this->line("  - {$permission->name} (guard: {$permission->guard_name})");
        }

        // Verificar roles
        $this->info("\n2. Verificando roles:");
        $roles = Role::all();
        $this->info("Total de roles: {$roles->count()}");

        foreach ($roles as $role) {
            $this->line("  - {$role->name} (guard: {$role->guard_name})");
            $this->line("    Permisos asignados: {$role->permissions->count()}");

            foreach ($role->permissions as $permission) {
                $this->line("      * {$permission->name}");
            }
        }

        // Verificar tabla role_has_permissions
        $this->info("\n3. Verificando tabla role_has_permissions:");
        $rolePermissions = DB::table('role_has_permissions')->get();
        $this->info("Total de registros en role_has_permissions: {$rolePermissions->count()}");

        foreach ($rolePermissions as $rp) {
            $role = Role::find($rp->role_id);
            $permission = Permission::find($rp->permission_id);
            $this->line("  - Rol: {$role->name} -> Permiso: {$permission->name}");
        }

        // Verificar tabla model_has_permissions
        $this->info("\n4. Verificando tabla model_has_permissions:");
        $modelPermissions = DB::table('model_has_permissions')->get();
        $this->info("Total de registros en model_has_permissions: {$modelPermissions->count()}");

        foreach ($modelPermissions as $mp) {
            $permission = Permission::find($mp->permission_id);
            $this->line("  - Modelo: {$mp->model_type} ID: {$mp->model_id} -> Permiso: {$permission->name}");
        }

        // Verificar tabla model_has_roles
        $this->info("\n5. Verificando tabla model_has_roles:");
        $modelRoles = DB::table('model_has_roles')->get();
        $this->info("Total de registros en model_has_roles: {$modelRoles->count()}");

        foreach ($modelRoles as $mr) {
            $role = Role::find($mr->role_id);
            $this->line("  - Modelo: {$mr->model_type} ID: {$mr->model_id} -> Rol: {$role->name}");
        }

        $this->info("\n=== Test completado ===");

        return 0;
    }
}
