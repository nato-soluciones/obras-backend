<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RolUserController extends Controller
{
    public function index()
    {
        $userRoles = Role::select('id', 'name', 'description')
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

}
