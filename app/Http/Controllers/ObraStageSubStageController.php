<?php

namespace App\Http\Controllers;

use App\Http\Requests\Obra\Stage\SubStage\StoreSubStageRequest;
use App\Http\Requests\Obra\Stage\SubStage\UpdateSubStageRequest;
use App\Http\Resources\ObraStageSubStageResource;
use App\Http\Services\ObraService;
use App\Http\Services\ObraStageService;
use App\Http\Services\ObraStageSubStageService;
use App\Http\Services\ObraStageSubStageTaskService;
use App\Models\ObraStage;
use App\Models\ObraStageSubStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageSubStageController extends Controller
{
    /**
     * Dependency injection
     */
    protected $obraStageSubStageService;

    public function __construct(ObraStageSubStageService $obraStageSubStageService)
    {
        $this->obraStageSubStageService = $obraStageSubStageService;
    }


    public function indexWithTasks(int $obraId, int $stageId)
    {
        // Verifica que la Stage existe
        $stage = ObraStage::where('id', $stageId)
            ->where('obra_id', $obraId)
            ->first();

        if (is_null($stage)) {
            return response()->json(['message' => 'Etapa no encontrada o no pertenece a esta obra'], 404);
        }

        // Recupera las SubStage con las tareas correspondientes
        $subStages = ObraStageSubStage::with(['tasks' => function ($q) {
            $q->orderBy('start_date', 'asc')
                ->orderBy('id', 'asc')
                ->with(['responsible' => function ($q1) {
                    $q1->select('id', 'firstname', 'lastname', 'deleted_at')
                        ->withTrashed();
                }])
                ->withCount('taskEvents');
        }])
            ->where('obra_stage_id', $stageId)
            ->orderBy('start_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Log::debug('ObraStageSubStageController::indexWithTasks: ' . json_encode($subStages));
        return ObraStageSubStageResource::collection($subStages);
    }

    public function store(StoreSubStageRequest $request, int $obraId, int $stageId)
    {
        try {
            $subStage = $this->obraStageSubStageService->store($request, $obraId, $stageId);
            
            if ($subStage instanceof \Exception) {
                throw $subStage;
            }
            if ($subStage['status'] === 'error') {
                return response($subStage, 404);
            }

            return response()->json($subStage, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error: ' . $e->getMessage());
            return response()->json(['message' => '(db) Error al crear la Sub-Etapa'], 500);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear la Sub-Etapa'], 500);
        }
    }


    public function update(UpdateSubStageRequest $request, int $obraId, int $stageId, int $subStageId)
    {
        try {
            $subStage = $this->obraStageSubStageService->update($request, $obraId, $stageId, $subStageId);
            return response()->json($subStage, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar la tarea'], 500);
        }
    }

    public function destroy(int $obraId, int $stageId, int $subStageId)
    {
        try {
            $subStage = $this->obraStageSubStageService->delete($obraId, $stageId, $subStageId);

            if ($subStage instanceof \Exception) {
                throw $subStage;
            }

            return response()->json($subStage, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear la Sub-Etapa'], 500);
        }
    }
}
