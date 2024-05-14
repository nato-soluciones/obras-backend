<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class permission_dev extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado
        // $user = Auth::user();
        // Obtener los roles del usuario
        // $userRoles = $user->getRoleNames();


        $allRoles = Role::all();
        $userRoles = Role::whereDoesntHave('permissions')->where('name', 'not like', 'functional\_%')->pluck('description', 'name')->toArray();
        $functionalRoles = Role::whereHas('permissions')->pluck('name')->toArray();
        $allPermitions = Permission::all();

        $response = [
            // 'user' => $user,
            'allRoles' => $allRoles,
            'userRoles' => $userRoles,
            'functionalRoles' => $functionalRoles,
            'allPermitions' => $allPermitions
        ];
        return response($response);
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
        $userRol = Role::where('name', 'OWNER')->first();
        $functionalRol = Role::where('name', 'functional_navbar_full')->get();

        $response = [
            'userRol' => $userRol,
            'functionalRol' => $functionalRol
        ];
        return response($response);
    }

    public function destroy($id)
    {
    }
}
