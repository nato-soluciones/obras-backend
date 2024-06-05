<?php

namespace App\Http\Controllers;

use App\Models\RoleRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RolUserController extends Controller
{
    public function index()
    {
        $userRoles = Role::where('name', 'not like', 'functional\_%')
            ->select('id', 'name', 'description')
            ->orderBy('description', 'asc')
            ->get();

        // Si el usuario logueado no es superadmin quita el rol SUPERADMIN
        if (!auth()->user()->hasRole('SUPERADMIN')) {
            $userRoles = $userRoles->filter(function ($userRole) {
                return $userRole->name !== 'SUPERADMIN';
            });
        }

        return response($userRoles);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['guard_name'] = 'api';

        // Check name is unique
        if (Role::where('name', $data['name'])->exists()) {
            return response(['message' => 'Ya existe un rol con el código ingresado.'], 409);
        }
        $data['name'] = strtoupper($data['name']);
        $user = Role::create($data);

        return response($user, 201);
    }

    public function show(int $id)
    {
        $user = Role::select("id", "name", "description")->find($id);

        return response($user, 200);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->all();
        unset($data['name']);

        $user = Role::find($id);
        $user->update($data);

        return response($user, 200);
    }

    public function usersAssociated(int $id)
    {
        $users = Role::find($id)->users()->select('id', 'firstname', 'lastname', 'email')->get()->makeHidden('pivot');

        return response($users, 200);
    }

    public function functionalRoleAssociated(int $id)
    {
        $roles = RoleRelationship::where('user_role_id', $id)
            ->with(['functionalRole' => function ($query) {
                $query->select('id', 'description', 'name')
                    ->orderBy('description', 'asc');
            }])
            ->get()
            ->pluck('functionalRole');

        return response($roles, 200);
    }

    public function functionalRoleAdd(Request $request, int $idRolUsuario)
    {
        // TODO: validar que el rol de usuario exista, que sea de usuario y que no sea SUPERADMIN
        $userRol = Role::find($idRolUsuario);

        $rolesFuncionales = $request->all();
        // Recorrer los roles y validar que existan el rol
        foreach ($rolesFuncionales as $rolFuncional) {
            $functionalRolExist = Role::where('name', $rolFuncional)->exists();
            if (!$functionalRolExist) {
                return response(['error' => "El rol funcional '{$rolFuncional}' no existe. No se asignó ningún rol funcional al rol '{$userRol->name}'"], 400);
            }
        }
        // Asocia los roles funcionales al rol de usuario
        $roleFunctionals = Role::whereIn('name', $rolesFuncionales)->get();
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
            $functionalRolExist = Role::where('name', $rolFuncional)->exists();
            if (!$functionalRolExist) {
                return response(['error' => "El rol funcional '{$rolFuncional}' no existe. No se eliminó ningún rol funcional al rol '{$userRol->name}'"], 400);
            }
        }

        // Elimina los roles funcionales al rol de usuario
        $roleFunctionals = Role::whereIn('name', $rolesFuncionales)->get();
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
