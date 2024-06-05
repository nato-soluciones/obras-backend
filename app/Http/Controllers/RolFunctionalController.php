<?php

namespace App\Http\Controllers;

use App\Models\RoleRelationship;
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
            ->orderBy('description', 'asc')
            ->get();
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

        // TODO: COntrolar que el name del permiso comience con 'functional\_'

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

    public function permissionsAssociated(int $id)
    {
        $users = Role::find($id)
            ->permissions()
            ->select('id', 'name', 'description')
            ->orderBy('description', 'asc')
            ->get()
            ->makeHidden('pivot');

        return response($users, 200);
    }

    public function userRolesAssociated(int $id)
    {
        $roles = RoleRelationship::where('functional_role_id', $id)
            ->with(['userRole' => function ($query) {
                $query->select('id', 'description', 'name');
            }])
            ->get()
            ->pluck('userRole');

        return response($roles, 200);
    }

    public function permissionsAdd(Request $request, int $id)
    {
        $functionalRol = Role::find($id);

        //TODO: Verificar que sea un rol funcional

        $permissions = $request->all();

        //TODO: si no hay permisos no hacer nada

        foreach ($permissions as $permission) {
            $permissionExists = Permission::where('name', $permission)->exists();
            if (!$permissionExists) {
                return response(['error' => "El permiso '{$permission}' no existe. No se asignó ningún permiso al rol '{$functionalRol->name}'."], 400);
            }
        }

        // Asigna los permisos a un rol funcional
        $functionalRol->givePermissionTo($permissions);

        return response(['message' => 'Alta de permisos exitosa'], 200);
    }

    public function permissionsRemove(Request $request, int $id)
    {
        $functionalRol = Role::find($id);

        // TODO: Verificar que sea un rol funcional

        $permissions = $request->all();

        // TODO: si no hay permisos no hacer nada

        foreach ($permissions as $permission) {
            $permissionExists = Permission::where('name', $permission)->exists();
            if (!$permissionExists) {
                return response(['error' => "El permiso '{$permission}' no existe. No se eliminó ningún permiso al rol '{$functionalRol->name}'."], 400);
            }
        }

        // Quita los permisos a un rol funcional
        $functionalRol->revokePermissionTo($permissions);

        return response(['message' => 'Eliminación de permisos exitosa'], 200);
    }
}
