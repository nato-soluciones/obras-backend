<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdatePassUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\User;


class UserController extends Controller
{
    /**
     * Get all the users
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();
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
        // Check email is unique
        if (User::where('email', $data['email'])->exists()) {
            return response(['message' => 'MSG:Ya existe un usuario con el E-mail ingresado.'], 409);
        }

        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);
        $user->markEmailAsVerified();
        return response($user, 201);
    }

    /**
     * Get a user by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        $user = User::find($id);
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
        $user = User::find($id);
        $user->update($data);
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
}
