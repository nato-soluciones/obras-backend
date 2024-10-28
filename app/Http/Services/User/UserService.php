<?php

namespace App\Http\Services\User;

use Illuminate\Support\Facades\Auth;

class UserService
{
    public function entityCheck($entity){
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $roles = $user->getRoleNames();

        // Verificar si el usuario tiene roles antes de acceder al índice 0
        if ($roles->isNotEmpty() && strtoupper($roles[0]) === 'SUPERADMIN') {
            return ['full'];
        }

        $permissions = $user->getAllPermissions();
        
        // Filtra los permisos por entidad
        $entityPermissions = $permissions->filter(function ($permission) use ($entity) {
            return strpos($permission->name, $entity . '_') === 0;
        })->pluck('name')->toArray();

        // Obtiene solo la acción de los permisos
        $actions = array_map(function ($permission) use ($entity) {
            return substr($permission, strlen($entity) + 1);
        }, $entityPermissions);

        return array_unique($actions);
    }
}