<?php

namespace App\Http\Controllers;

use App\Models\StoreMovementConcept;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreMovementConceptController extends Controller
{
    public function index()
    {
        $movementConcepts = StoreMovementConcept::get();

        return response($movementConcepts, 200);
    }

    public function show(string $id): Response
    {
        $concept = StoreMovementConcept::find($id);

        if (!$concept) {
            return response([
                'error' => 'Concepto de movimiento no encontrado'
            ], 404);
        }

        return response($concept, 200);
    }
}
