<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $allPermissions = Permission::select('id', 'name', 'description')->get();
        return response($allPermissions);
    }
}
