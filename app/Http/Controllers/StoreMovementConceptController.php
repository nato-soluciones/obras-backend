<?php

namespace App\Http\Controllers;

use App\Models\StoreMovementConcept;
use Illuminate\Http\Request;

class StoreMovementConceptController extends Controller
{
    public function index()
    {
        $movementConcepts = StoreMovementConcept::get();

        return response($movementConcepts, 200);
    }

    public function show($id)
    {
        $storeMovementConcept = StoreMovementConcept::find($id);
        if (!$storeMovementConcept) {
            return response()->json(['error' => 'No se encontró el concepto de movimiento'], 404);
        }

        return response()->json($storeMovementConcept, 200);
    }
}
