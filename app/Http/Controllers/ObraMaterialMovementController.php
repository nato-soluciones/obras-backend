<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Obra;
use App\Models\ObraMaterialMovement;
use Illuminate\Http\Request;

class ObraMaterialMovementController extends Controller
{
    public function index(Request $request,  int $obraId, int $obraMaterialId)
    {
        // Verifica que la obra exista
        $obra = Obra::find($obraId);
        if (is_null($obra)) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        $obraMaterialMovements = ObraMaterialMovement::from('obra_material_movements as omm')
        ->join('measurement_units as mu', 'omm.measurement_unit_id', '=', 'mu.id')
        ->where('omm.obra_material_id', $obraMaterialId)
        ->select(
            'omm.id as movement_id',
            'omm.date',
            'omm.movement_type',
            'omm.quantity',
            'omm.description',
            'omm.observation',
            'mu.abbreviation as unit_abbreviation'
        )
        ->get();

        return response($obraMaterialMovements, 200);
    }

    public function store(Request $request)
    {
        //
    }
}
