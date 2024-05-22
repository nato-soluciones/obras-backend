<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolFunctionalController extends Controller
{
    public function index()
    {
        $userRoles = Role::Where('name', 'like', 'functional\_%')
            ->select('id', 'name', 'description')
            ->get();
        return response($userRoles);
    }

    public function permissionsAdd(Request $request, int $id)
    {
        $functionalRol = Role::find($id);

        //TODO: Verificar que sea un rol funcional

        $permissions = $request->all();

        //TODO: si no hay permisos no hacer nada

        foreach ($permissions as $permission) {
            $permissionExists = Permission::where('name', $permission['name'])->exists();
            if (!$permissionExists) {
                return response(['error' => "El permiso '{$permission['name']}' no existe. No se asignó ningún permiso al rol '{$functionalRol->name}'."], 400);
            }
        }


        // Asigna los permisos a un rol funcional
        $permissionsSave = array_column($permissions, 'name');
        $functionalRol->givePermissionTo($permissionsSave);

        return response(['message' => 'Alta de permisos exitosa'], 200);
    }

    public function permissionsRemove(Request $request, int $id)
    {
        $functionalRol = Role::find($id);

        //TODO: Verificar que sea un rol funcional

        $permissions = $request->all();

        //TODO: si no hay permisos no hacer nada

        foreach ($permissions as $permission) {
            $permissionExists = Permission::where('name', $permission['name'])->exists();
            if (!$permissionExists) {
                return response(['error' => "El permiso '{$permission['name']}' no existe. No se eliminó ningún permiso al rol '{$functionalRol->name}'."], 400);
            }
        }


        // Asigna los permisos a un rol funcional
        $permissionsSave = array_column($permissions, 'name');
        $functionalRol->revokePermissionTo($permissionsSave);

        return response(['message' => 'Eliminación de permisos exitosa'], 200);
    }
}
