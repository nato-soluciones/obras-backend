<?php

namespace App\Http\Controllers;

use App\Models\StoreMovementType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreMovementTypeController extends Controller
{
    public function index()
    {
        $movementTypes = StoreMovementType::get();

        return response($movementTypes, 200);
    }

    public function indexWithConcepts()
    {
        $types = StoreMovementType::with('concepts')->get();

        return response($types, 200);
    }

    public function show(string $id): Response
    {
        $type = StoreMovementType::find($id);

        if (!$type) {
            return response([
                'error' => 'Tipo de movimiento no encontrado'
            ], 404);
        }

        return response($type, 200);
    }
}
