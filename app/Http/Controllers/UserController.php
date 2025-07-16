<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdatePassUserRequest;
use App\Http\Services\NotificationSettingsService;
use App\Http\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
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

    public function password(UpdatePassUserRequest $request, int $id)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        $user = User::find($id);
        $user->update($data);
        return response($user, 200);
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response(['message' => 'Usuario eliminado correctamente.'], 204);
    }

    public function permissionsCheck(Request $request)
    {
        $permission = $request->input('permission', '');
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userPermission = $user->hasPermissionTo($permission);
        return response($userPermission, 200);
    }

    public function entityCheck(Request $request, UserService $userService)
    {
        try {
            $entity = $request->input('entity', '');
            $actions = $userService->entityCheck($entity);
            return response($actions, 200);
        } catch (\Exception $e) {
            return response(['message' => 'Error al obtener los permisos.'], 500);
        }
    }

    /**
     * Get users list for combo/select components
     */
    public function listCombo()
    {
        $users = User::select('id', 'firstname', 'lastname')
            ->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'SUPERADMIN');
            })
            ->orWhereDoesntHave('roles')
            ->orderByRaw("CONCAT(COALESCE(firstname, ''), ' ', COALESCE(lastname, ''))")
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''))
                ];
            });

        return response()->json($users);
    }

    /**
     * Search users for calendar participants
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $limit = min($request->input('limit', 10), 50); // Max 50 results

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->select('id', 'name', 'email')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
