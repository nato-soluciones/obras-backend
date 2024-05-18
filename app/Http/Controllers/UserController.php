<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdatePassUserRequest;
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
        }])->get();

        // Ocultar la propiedad pivot de los roles
        $users->each(function ($user) {
            $user->makeHidden('permissions');
            $user->roles->makeHidden('pivot');
            $user->roles->makeHidden('permissions');
        });
        return response($users, 200);
    }

    /**
     * Create a user
     * and verify the email
     *
     * @param Request $request
     * @return Response
     */
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
        $user = User::find($id);
        $user->delete();
        return response(['message' => 'User deleted'], 204);
    }

    public function permissionsCheck(Request $request)
    {
        $permission = $request->input('permission','');
        $user = Auth::user();
        $userPermission = $user->hasPermissionTo($permission);
        return response($userPermission, 200);
    }

    public function entityCheck(Request $request)
    {
        $entity = $request->input('entity', '');

        $user = Auth::user();
        if(strtoupper($user->getRoleNames()[0]) === 'SUPERADMIN') return Response(['full'], 200);

        $permissions = $user->getAllPermissions();
 
        // Filtra los permisos por entidad
        $entityPermissions = $permissions->filter(function ($permission) use ($entity) {
            return strpos($permission->name, $entity . '_') === 0;
        })->pluck('name')->toArray();

        // Obtiene solo la acción de los permisos
        $actions = array_map(function ($permission) use ($entity) {
            return substr($permission, strlen($entity) + 1);
        }, $entityPermissions);

        return Response(array_unique($actions), 200);
    }
}
