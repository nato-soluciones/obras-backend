<?php

namespace App\Http\Controllers;

use App\Models\RoleRelationship;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

class RolUserController extends Controller
{
    public function index()
    {
        $userRoles = Role::where('name', 'not like', 'functional\_%')
            ->select('id', 'name', 'description')
            ->get();
        return response($userRoles);
    }

    public function functionalRoleAdd(Request $request, int $idRolUsuario)
    {
        // TODO: validar que el rol de usuario exista, que sea de usuario y que no sea SUPERADMIN
        $userRol = Role::find($idRolUsuario);

        $rolesFuncionales = $request->all();
        // Recorrer los roles y validar que existan el rol
        foreach ($rolesFuncionales as $rolFuncional) {
            $functionalRolExist = Role::where('name', $rolFuncional['name'])->exists();
            if (!$functionalRolExist) {
                return response(['error' => "El rol funcional '{$rolFuncional['name']}' no existe. No se asignó ningún rol funcional al rol '{$userRol->name}'"], 400);
            }
        }

        $rolesFuncionalesSave = array_column($rolesFuncionales, 'name');

        // Asocia los roles funcionales al rol de usuario
        $roleFunctionals = Role::whereIn('name', $rolesFuncionalesSave)->get();
        foreach ($roleFunctionals as $roleFunctional) {
            if ($roleFunctional->permissions->count() > 0) {
                RoleRelationship::FirstOrCreate([
                    'functional_role_id' => $roleFunctional['id'],
                    'user_role_id' => $idRolUsuario
                ]);

                $userRol->givePermissionTo($roleFunctional->permissions()->pluck('id'));
            }
        }
        return response(['message' => 'Alta de roles funcionales exitosa'], 200);
    }

    public function functionalRoleRemove(Request $request, int $idRolUsuario)
    {
        // TODO: validar que el rol de usuario exista, que sea de usuario y que no sea SUPERADMIN
        $userRol = Role::find($idRolUsuario);

        $rolesFuncionales = $request->all();
        // Recorrer los roles y validar que existan el rol
        foreach ($rolesFuncionales as $rolFuncional) {
            $functionalRolExist = Role::where('name', $rolFuncional['name'])->exists();
            if (!$functionalRolExist) {
                return response(['error' => "El rol funcional '{$rolFuncional['name']}' no existe. No se eliminó ningún rol funcional al rol '{$userRol->name}'"], 400);
            }
        }

        $rolesFuncionalesNameRemove = array_column($rolesFuncionales, 'name');
        $roleFunctionals = Role::whereIn('name', $rolesFuncionalesNameRemove)->get();

        // Elimina los roles funcionales al rol de usuario
        foreach ($roleFunctionals as $roleFunctional) {
            if ($roleFunctional->permissions->count() > 0) {
                // Elimina la asociación entre el rol de usuario y el rol funcional
                RoleRelationship::where('user_role_id', $idRolUsuario)
                    ->where('functional_role_id', $roleFunctional['id'])
                    ->delete();

                // Elimina los permisos del rol funcional
                $userRol->revokePermissionTo($roleFunctional->permissions()->pluck('id'));
            }
        }

        return response(['message' => 'Baja de roles funcionales exitosa'], 200);
    }
}
