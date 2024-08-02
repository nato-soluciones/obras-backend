<?php

namespace App\Http\Controllers;

use App\Models\ObraStage;
use App\Http\Services\ObraService;
use App\Http\Services\ObraStageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageController extends Controller
{
    /**
     * Dependency injection
     */
    protected $obraStageService;

    public function __construct(ObraStageService $obraStageService)
    {
        $this->obraStageService = $obraStageService;
    }
    
    public function index(int $obraId)
    {
        $stages = ObraStage::where('obra_id', $obraId)
            ->orderBy('start_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return response($stages, 200);
    }

    public function indexGantt(int $obraId)
    {
        $stages = ObraStage::with(['subStages.tasks'])
            ->where('obra_id', $obraId)
            ->orderBy('start_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return response($stages, 200);
    }

    public function store(Request $request, int $obraId)
    {
        try {
            $stage = $this->obraStageService->store($request, $obraId);

            if ($stage instanceof \Exception) {
                throw $stage;
            }

            return response()->json($stage, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error: ' . $e->getMessage());
            return response()->json(['message' => '(db) Error al crear la Etapa'], 500);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear la Etapa'], 500);
        }
    }

    public function show($obraId, $stageId): Response
    {
        $obraStage = ObraStage::where('obra_id', $obraId)->find($stageId);
        return response($obraStage, 200);
    }

    public function update(Request $request, $obraId, $stageId)
    {
        try {
            $stage = $this->obraStageService->update($request, $obraId, $stageId);
            return response()->json($stage, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar la etapa'], 500);
        }
    
    }

    public function destroy(int $obraId, $stageId)
    {
        try {
            $this->obraStageService->destroy($obraId, $stageId);
            return response()->json(['message' => 'Etapa eliminada con éxito'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al eliminar la etapa'], 500);
        }
    }
}
