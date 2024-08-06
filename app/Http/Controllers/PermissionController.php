<?php

namespace App\Http\Controllers;

use App\Enums\Permission as EnumsPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $allPermissions = Permission::select('id', 'name', 'description')->get();
        return response($allPermissions);
    }

    public function permissionsByRole(int $roleId)
    {
        // Obtener el rol por ID
        $role = Role::find($roleId);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Obtener los permisos del rol
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        // Obtener todos los permisos
        $permissions = Permission::orderBy('name', 'asc')->get();

        // Estructurar los permisos por entidad
        $structuredPermissions = [];

        foreach ($permissions as $permission) {
            $parts = explode('_', $permission->name);
            if (count($parts) == 2) {
                $entity = $parts[0];
                $action = $parts[1];

                $actionName = $entity === 'navbar'
                    ? (array_key_exists($action, EnumsPermission::$entities) ? EnumsPermission::$entities[$action] : $action)
                    : (array_key_exists($action, EnumsPermission::$actions) ? EnumsPermission::$actions[$action] : $action);

                $structuredPermissions[$entity][] = [
                    'action' => $actionName,
                    'permission' => $permission->name,
                    'active' => in_array($permission->name, $rolePermissions),
                ];
            }
        }

        // Convertir el array estructurado en una lista de entidades
        $result = [];
        foreach ($structuredPermissions as $entity => $permissions) {
            $result[] = [
                'entity' => $entity,
                'name' => array_key_exists($entity, EnumsPermission::$entities) && EnumsPermission::$entities[$entity] !== ''  ? EnumsPermission::$entities[$entity] : $entity,
                'permissions' => $permissions
            ];
        }

        return response()->json($result);
    }

    public function updatePermissionsByRole(Request $request, int $roleId)
    {

        // Obtener el rol por ID
        $role = Role::find($roleId);

        if (!$role) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        $permissionsList = $request->all();

        foreach ($permissionsList as $perm) {
            if($perm['active']){
                $role->givePermissionTo($perm['permission']);
            }else{
                $role->revokePermissionTo($perm['permission']);
            }
        }

        return response(['message' => 'Permisos actualizados correctamente'], 200);
    }
}
