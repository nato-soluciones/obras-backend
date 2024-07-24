<?php

namespace App\Http\Controllers;

use App\Models\CurrentAccountMovementType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CurrentAccountMovementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validEntity = ['CLIENT', 'PROVIDER'];
        $entity = $request->input('entity');

        if (!in_array($entity, $validEntity)) {
            return response(['message' => 'Entidad inválida'], 200);
        }

        $movementTypes = CurrentAccountMovementType::select('id', 'name', 'type')
            ->where('active', true)
            ->where('entity_type', $entity)
            ->where('system_type', false)
            ->get();


        return response($movementTypes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
