<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdatePassUserRequest;
use App\Http\Services\NotificationSettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Get all the users
     *
     * @return Response
     */
    public function index()
    {
        $users = User::with(['roles' => function ($q) {
            $q->select("name", "description");
        }])->whereHas('roles', function ($query) {
            $query->where('name', '!=', 'SUPERADMIN');
        })->orWhereDoesntHave('roles')
            ->get();

        // Ocultar la propiedad pivot de los roles
        $users->each(function ($user) {
            $user->makeHidden('permissions');
            $user->roles->makeHidden('pivot');
            $user->roles->makeHidden('permissions');
        });
        return response($users, 200);
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->all();
        $role = $data['role'];
        unset($data['role']);

        // Check email is unique
        if (User::where('email', $data['email'])->exists()) {
            return response(['message' => 'MSG:Ya existe un usuario con el E-mail ingresado.'], 409);
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->syncRoles($role);

        $user->markEmailAsVerified();
        return response($user, 201);
    }

    public function show(int $id)
    {
        $user = User::with(['roles' => function ($q) {
            $q->select("name", "description");
        }])->find($id);

        // Ocultar la propiedad pivot de los roles
        if ($user) {
            $user->roles->makeHidden('pivot');
        }

        return response($user, 200);
    }
    public function showWithPermissions(int $id)
    {
        $user = User::with(['roles', 'roles.permissions' => function ($q) {
            $q->select("name", "description");
        }])->find($id);
        // Ocultar la propiedad pivot de los roles
        if ($user) {
            $user->roles->makeHidden('pivot');
        }
        return response($user, 200);
    }

    public function settings(int $userId, NotificationSettingsService $notificationSettingsService)
    {
        $user = User::with(['roles' => function ($q) {
            $q->select("name", "description");
        }])->find($userId);

        if ($user) {
            // aplana los datos del usuario
            $user = $user->toArray();
            $user['role_name'] = $user['roles'][0]['name'];
            $user['role_description'] = $user['roles'][0]['description'];
            unset($user['roles']);

            $notifications = $notificationSettingsService->getUserNotificationSettings($userId);
        }

        $response = [
            'profile' => $user,
            'notificationsList' => $notifications
        ];

        return response($response, 200);
    }

    /**
     * Update a user by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $data = $request->all();
        $role = $data['role'];
        unset($data['role']);

        $user = User::find($id);
        $user->update($data);
        $user->syncRoles($role);

        return response($user, 200);
    }

    /**
     * Update a user password by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function password(UpdatePassUserRequest $request, int $id)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        $user = User::find($id);
        $user->update($data);
        return response($user, 200);
    }

    /**
     * Delete a user by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response(['message' => 'Usuario eliminado correctamente.'], 204);
    }

    public function permissionsCheck(Request $request)
    {
        $permission = $request->input('permission', '');
        $user = Auth::user();
        $userPermission = $user->hasPermissionTo($permission);
        return response($userPermission, 200);
    }

    public function entityCheck(Request $request)
    {
        $debug = false;
        if ($debug) {
            Log::debug('---------- entityCheck INICIO ----------');
            Log::debug('Request: ' . json_encode($request->all()));
        }
        $entity = $request->input('entity', '');

        $user = Auth::user();
        if ($debug) Log::debug('User: ' . json_encode($user));

        $roles = $user->getRoleNames();

        // Verificar si el usuario tiene roles antes de acceder al índice 0
        if ($roles->isNotEmpty() && strtoupper($roles[0]) === 'SUPERADMIN') {
            if ($debug) Log::debug('RETURN Superadmin');
            return response(['full'], 200);
        }

        $permissions = $user->getAllPermissions();
        // Filtra los permisos por entidad
        $entityPermissions = $permissions->filter(function ($permission) use ($entity) {
            return strpos($permission->name, $entity . '_') === 0;
        })->pluck('name')->toArray();

        if ($debug) Log::debug('ENTITY Permissions: ' . json_encode($entityPermissions));
        // Obtiene solo la acción de los permisos
        $actions = array_map(function ($permission) use ($entity) {
            return substr($permission, strlen($entity) + 1);
        }, $entityPermissions);

        if ($debug) Log::debug('RETURN Actions: ' . json_encode($actions));
        if ($debug) Log::debug('---------------- FIN ---------------------');
        return response(array_unique($actions), 200);
    }
}
