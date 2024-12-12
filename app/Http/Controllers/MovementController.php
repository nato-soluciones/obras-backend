<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movement\StoreMovementRequest;
use Illuminate\Http\Request;
use App\Models\Movement;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $movements = Movement::select('id', 'from_store_id', 'to_store_id', 'material_id', 'quantity', 'status')->get();

        return response($movements, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovementRequest $request): Response
    {
        $movement = Movement::create($request->validated());
        return response($movement, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $movement = Movement::select('id', 'from_store_id', 'to_store_id', 'material_id', 'quantity', 'status')->find($id);

        return response($movement, 200);
    }
}
