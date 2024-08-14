<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionMigrationV1_2_1ToV1_3_0 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ? Elimina permisos viejos
        $permissions = [
            "obraStageTasks_list",
            "obraStageTasks_insert",
            "obraStageTasks_update",
            "obraStageTasks_delete",
            "obraStageTasks_display",
            "navbar_calendar",
            "navbar_exchange_rate"
        ];

        foreach ($permissions as $permission) {
            $roles = Role::whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })->get();

            foreach ($roles as $role) {
                $role->revokePermissionTo($permission);
            }

            // Luego elimina el permiso
            try {
                Permission::findByName($permission, 'api')->delete();
            } catch (\Throwable $th) {
                continue;
            }
        }
    }
}
