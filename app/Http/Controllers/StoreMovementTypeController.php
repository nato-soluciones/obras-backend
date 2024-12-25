<?php

namespace App\Http\Controllers;

use App\Models\StoreMovementType;
use Illuminate\Http\Request;

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

    public function show($id)
    {
        $storeMovementType = StoreMovementType::with('concepts')->find($id);
        if (!$storeMovementType) {
            return response()->json(['error' => 'No se encontró el tipo de movimiento'], 404);
        }

        return response()->json($storeMovementType, 200);
    }
}
