<?php

namespace App\Http\Controllers;

use App\Http\Requests\Obra\Stage\SubStage\Task\StoreTaskRequest;
use App\Http\Requests\Obra\Stage\SubStage\Task\UpdateTaskRequest;
use App\Http\Services\ObraStageService;
use App\Http\Services\ObraStageSubStageTaskService;
use App\Models\ObraStageSubStageTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageSubStageTaskController extends Controller
{
    /**
     * Dependency injection
     */
    protected $obraStageService;
    protected $obraStageSubStageTaskService;

    public function __construct(ObraStageService $obraStageService, ObraStageSubStageTaskService $obraStageSubStageTaskService)
    {
        $this->obraStageService = $obraStageService;
        $this->obraStageSubStageTaskService = $obraStageSubStageTaskService;
    }

    public function store(StoreTaskRequest $request, int $obraId, int $stageId, int $subStageId)
    {
        try {
            $this->obraStageSubStageTaskService->store($request, $obraId, $stageId, $subStageId);
            return response()->json(['message' => 'Tarea creada correctamente'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear la tarea'], 500);
        }
    }

    public function show(int $obraId, int $stageId, int $subStageId, int $taskId)
    {
        $obraStageTask = ObraStageSubStageTask::with('responsible')->find($taskId);
        return response($obraStageTask, 200);
    }

    public function update(UpdateTaskRequest $request, int $obraId, int $stageId, int $subStageId, int $taskId)
    {
        try {
            $task = $this->obraStageSubStageTaskService->update($request, $obraId, $stageId, $subStageId, $taskId);
            return response()->json($task, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar la tarea'], 500);
        }
    }


    public function updateProgress(Request $request, int $obraId, int $stageId, int $subStageId, int $taskId)
    {
        try {
            $task = $this->obraStageSubStageTaskService->updateProgress($request, $obraId, $stageId, $subStageId, $taskId);
            return response()->json($task, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar la tarea'], 500);
        }
    }

    public function destroy(int $obraId, int $stageId, int $subStageId, int $taskId)
    {
        try {
            $response = $this->obraStageSubStageTaskService->delete($obraId, $stageId, $subStageId, $taskId);

            return response()->json($response, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al eliminar la tarea'], 500);
        }
    }

    public function getQualityControl(int $obraId, int $stageId, int $subStageId, int $taskId)
    {
        try {
            $response = $this->obraStageSubStageTaskService->getQualityControl($obraId, $stageId, $subStageId, $taskId);
            return response()->json($response, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al obtener el control de calidad'], 500);
        }
    }
}
