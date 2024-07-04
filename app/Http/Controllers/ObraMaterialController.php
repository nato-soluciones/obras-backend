<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Models\ObraMaterial;
use App\Models\ObraMaterialMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObraMaterialController extends Controller
{

    public function index(Request $request, int $obraId)
    {
        // Verifica que la obra exista
        $obra = Obra::find($obraId);
        if (is_null($obra)) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        $materials = ObraMaterial::from('obra_materials as om')
            ->join('materials as m', 'om.material_id', '=', 'm.id')
            ->join('measurement_units as mu', 'm.measurement_unit_id', '=', 'mu.id')
            ->where('om.obra_id', $obraId)
            ->select(
                'om.id as obra_material_id',
                'om.quantity',
                'm.id as material_id',
                'm.name as material_name',
                'mu.id as unit_id',
                'mu.name as unit_name',
                'mu.abbreviation as unit_abbreviation',
            )
            ->orderBy('m.name', 'asc')
            ->orderBy('m.id', 'asc')
            ->get();
        return response($materials, 200);
    }

    public function show(int $obraId, int $obraMaterialId)
    {
        // Verifica que la obra exista
        $obra = Obra::find($obraId);
        if (is_null($obra)) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        $material = ObraMaterial::from('obra_materials as om')
            ->join('obras as o', 'om.obra_id', '=', 'o.id')
            ->join('materials as m', 'om.material_id', '=', 'm.id')
            ->join('measurement_units as mu', 'm.measurement_unit_id', '=', 'mu.id')
            ->where('om.id', $obraMaterialId)
            ->select(
                'om.id as obra_material_id',
                'om.quantity',
                'o.id as obra_id',
                'o.name as obra_name',
                'm.name as material_name',
                'mu.name as unit_name',
                'mu.abbreviation as unit_abbreviation',
            )
            ->first();

        return response($material, 200);
    }

    public function store(Request $request, int $obraId)
    {
        $materialMovements = $request->all();

        // recorre los movimientos de materiales y crea un nuevo movimiento de material para cada uno
        try {
            DB::transaction(function () use ($materialMovements, $obraId) {
                foreach ($materialMovements as $materialMovement) {

                    $obraMaterial = ObraMaterial::firstOrCreate(
                        ['obra_id' => $obraId, 'material_id' => $materialMovement['material_id']],
                        ['quantity' => 0]
                    );

                    ObraMaterialMovement::create([
                        'obra_material_id' => $obraMaterial->id,
                        'date' => $materialMovement['date'],
                        'movement_type' => $materialMovement['movement_type_id'],
                        'measurement_unit_id' => $materialMovement['measurement_unit_id'],
                        'quantity' => $materialMovement['quantity'],
                        'description' => $materialMovement['description'],
                        'observation' => $materialMovement['observation'],
                        'created_by_id' => auth()->id()
                    ]);

                    // actualiza la cantidad del material
                    $newQuantity = strtoupper($materialMovement['movement_type_id']) === 'ACOPIO'
                        ? $obraMaterial->quantity + $materialMovement['quantity']
                        : $obraMaterial->quantity - $materialMovement['quantity'];

                    $obraMaterial->update(['quantity' => $newQuantity]);
                }

                return response()->json(['message' => 'Movimientos de materiales creados correctamente'], 200);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear los movimientos de materiales'], 500);
        }
    }
}
