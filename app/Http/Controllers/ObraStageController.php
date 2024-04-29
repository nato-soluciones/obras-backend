<?php

namespace App\Http\Controllers;

use App\Models\ObraStage;
use App\Services\ObraService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ObraStageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $obraId)
    {
        $incomes = ObraStage::where('obra_id', $obraId)->get();
        return response($incomes, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->merge(['progress' => 0]);
            $request->merge(['created_by_id' => auth()->user()->id]);

            $obraStage = ObraStage::create($request->all());

            // Obtiene una instancia del servicio ObraService y actualiza el progreso de la obra
            $obraService = app(ObraService::class);
            $obraService->updateObraProgress($obraStage->obra);

            return response($obraStage, 201);
        } catch (\Exception $e) {
            Log::error("Error al crear la etapa {$request->name}: " . $e->getMessage());
            return response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($obraId, $stageId): Response
    {
        $obraStage = ObraStage::find($stageId);
        return response($obraStage, 200);
    }


    /**
     * Show the form for update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($obraId, $stageId)
    {
        $obraStage = ObraStage::find($stageId);
        if (!$obraStage) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }
        // Obtiene el valor actual del campo obra_porcentaje
        $oldPercentage = $obraStage->obra_percentage;

        // Actualiza el registro
        $obraStage->update(request()->all());

        // Verific si el valor del campo obra_porcentaje ha cambiado
        if ($oldPercentage != $obraStage->obra_percentage) {
            // Actualiza el progreso de la obra
            $obraService = app(ObraService::class);
            $obraService->updateObraProgress($obraStage->obra);
        }
        return response($obraStage, 200);
    }

    /**
     * Delete a user by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $obraId, int $stageId)
    {
        $obraStage = ObraStage::find($stageId);
        $obraStage->delete();


        // Actualiza el porcentaje de la obra
        $obraService = app(ObraService::class);
        $obraService->updateObraProgress($obraStage->obra);

        return response(['message' => 'Obra Stage deleted'], 204);
    }
}
