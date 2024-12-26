<?php

namespace App\Http\Controllers;

use App\Models\UserStore;
use Illuminate\Http\Request;
use App\Http\Requests\UserStoreRequest;

class UserStoreController extends Controller
{
    public function index()
    {
        $userStores = UserStore::with(['user', 'store'])->get();
        return response()->json($userStores);
    }

    public function store(UserStoreRequest $request)
    {
        $userStore = UserStore::create($request->validated());
        return response()->json($userStore, 201);
    }

    public function show(UserStore $userStore)
    {
        return response()->json($userStore->load(['user', 'store']));
    }

    public function update(UserStoreRequest $request, UserStore $userStore)
    {
        $userStore->update($request->validated());
        return response()->json($userStore);
    }

    public function destroy(UserStore $userStore)
    {
        $userStore->delete();
        return response()->json(null, 204);
    }
} 