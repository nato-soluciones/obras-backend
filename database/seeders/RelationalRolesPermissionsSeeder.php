<?php

namespace Database\Seeders;

use App\Models\RoleRelationship;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RelationalRolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ? RELACIÓN DE USUARIOS CON ROLES DE USUARIO 
        $users = ["tomasgimenez11@gmail.com", "superadmin@gmail.com", "Arquitecto@gmail.com"];
        foreach ($users as $user) {
            $userRol = User::where('email', $user)->first();
            if ($userRol) {
                if ($userRol['email'] === "superadmin@gmail.com") $userRol->syncRoles("SUPERADMIN");
                if ($userRol['email'] === "Arquitecto@gmail.com") $userRol->syncRoles("ARCHITECT");
                if ($userRol['email'] === "tomasgimenez11@gmail.com") $userRol->syncRoles("OWNER");
            }
        }

        // ? AGREGA TODOS LOS PERMISOS AL ROL (DUEÑO)?
    }
}
