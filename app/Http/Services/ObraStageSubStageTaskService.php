<?php

namespace App\Http\Services;

use App\Http\Requests\Obra\Stage\SubStage\Task\StoreTaskRequest;
use App\Http\Requests\Obra\Stage\SubStage\Task\UpdateTaskRequest;
use App\Models\Obra;
use App\Models\ObraStageSubStageTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageSubStageTaskService
{
  protected $obraStageSubStageService;
  protected $obraStageService;
  protected $obraService;

  public function __construct(ObraService $obraService,  ObraStageService $obraStageService, ObraStageSubStageService $obraStageSubStageService)
  {
    $this->obraStageSubStageService = $obraStageSubStageService;
    $this->obraStageService = $obraStageService;
    $this->obraService = $obraService;
  }

  private function validateRelationships(int $obraId, int $stageId, int $subStageId)
  {
    $obra = Obra::with(['stages' => function ($query) use ($stageId, $subStageId) {
      $query->where('id', $stageId)->with(['subStages' => function ($query) use ($subStageId) {
        $query->where('id', $subStageId);
      }]);
    }])->find($obraId);

    if (!$obra) {
      throw ValidationException::withMessages(['obra' => 'La obra no existe.']);
    }

    $stage = $obra->stages->first();
    if (!$stage) {
      throw ValidationException::withMessages(['stage' => 'La etapa no existe o no pertenece a esta obra.']);
    }

    if (!$stage->subStages->first()) {
      throw ValidationException::withMessages(['sub_stage' => 'La sub-etapa no existe o no pertenece a esta etapa.']);
    }

    return true;
  }

  public function store(StoreTaskRequest $request, int $obraId, int $stageId, int $subStageId)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $stageId, $subStageId);

    // Creamos la tarea
    $request->merge([
      'progress' => 0,
      'current_quantity' => 0,
      'obra_stage_id' => $stageId,
      'obra_stage_sub_stage_id' => $subStageId,
      'created_by_id' => auth()->user()->id
    ]);
    try {
      $response = DB::transaction(function () use ($request) {
        $task = ObraStageSubStageTask::create($request->all());

        // Actualiza el progreso de la SubEtapa
        $subStageProgress = $this->obraStageSubStageService->updateSubStageProgress($task->obraStageSubStage);
        // Actualiza el proceso de la Etapa
        $stageProgress = $this->obraStageService->updateStageProgress($task->obraStageSubStage->obraStage);
        // Actualiza el progreso de la Obra
        $this->obraService->updateObraProgress($task->obraStageSubStage->obraStage->obra);

        return [
          'stageProgress' => $stageProgress,
          'subStageProgress' => $subStageProgress,
        ];
      });
      return $response;
    } catch (\Exception $e) {
      Log::error('Error en la transacción: ' . $e->getMessage());
      return $e;
    }
  }

  public function update(UpdateTaskRequest $request, int $obraId, int $stageId, int $subStageId, int $taskId)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $stageId, $subStageId);

    $obraStageSubStageTask = ObraStageSubStageTask::where('id', $taskId)
      ->where('obra_stage_id', $stageId)
      ->where('obra_stage_sub_stage_id', $subStageId)
      ->firstOrFail();

    $obraStageSubStageTask->update($request->all());

    return $obraStageSubStageTask;
  }

  public function delete(int $obraId, int $stageId, int $subStageId, int $taskId)
  {
    $this->validateRelationships($obraId, $stageId, $subStageId);

    $task = ObraStageSubStageTask::where('id', $taskId)
      ->where('obra_stage_id', $stageId)
      ->where('obra_stage_sub_stage_id', $subStageId)
      ->firstOrFail();

    try {
      $response = DB::transaction(function () use ($task) {
        $task->delete();

        // Actualiza el progreso de la SubEtapa
        $subStageProgress = $this->obraStageSubStageService->updateSubStageProgress($task->obraStageSubStage);
        // Actualiza el proceso de la Etapa
        $stageProgress = $this->obraStageService->updateStageProgress($task->obraStageSubStage->obraStage);
        // Actualiza el progreso de la Obra
        $this->obraService->updateObraProgress($task->obraStageSubStage->obraStage->obra);
        
        return [
          'stageProgress' => $stageProgress,
          'subStageProgress' => $subStageProgress,
        ];
      });

      return $response;
    } catch (\Exception $e) {
      Log::error('Error en la transacción: ' . $e->getMessage());
      return $e;
    }
  }

  public function updateProgress(Request $request, int $obraId, int $stageId, int $subStageId, int $taskId)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $stageId, $subStageId);

    $task = ObraStageSubStageTask::where('id', $taskId)
      ->where('obra_stage_id', $stageId)
      ->where('obra_stage_sub_stage_id', $subStageId)
      ->firstOrFail();

    if ($task->progress_type === 'percentage') {
      if ($request->progress < 0 || $request->progress > 100) {
        throw ValidationException::withMessages(['progress' => 'El avance debe ser entre 0 y 100.']);
      }
      if ($request->progress < $task->progress) {
        throw ValidationException::withMessages(['current_quantity' => "El avance ({$request->progress}) debe ser mayor al valor anterior ({$task->progress})."]);
      }

      $taskUpdate = [
        'progress' => $request->progress
      ];

      if($request->progress === 100) {
        $taskUpdate['is_completed'] = true;
      }

      $task->update($taskUpdate);
    } else if ($task->progress_type === 'quantity') {
      if (!is_int($request->progress)) {
        throw ValidationException::withMessages(['current_quantity' => 'El avance debe ser un valor entero.']);
      }
      if ($request->progress < 0 || $request->progress > $task->max_quantity) {
        throw ValidationException::withMessages(['current_quantity' => 'El avance debe ser entre 0 y la cantidad máxima.']);
      }
      if ($request->progress < $task->current_quantity) {
        throw ValidationException::withMessages(['current_quantity' => "El avance ({$request->progress}) debe ser mayor al valor anterior ({$task->current_quantity})."]);
      }

      $taskUpdate = [
        'current_quantity' => $request->progress
      ];

      if ($request->progress === $task->max_quantity) {
        $taskUpdate['is_completed'] = true;
      }

      $task->update($taskUpdate);
    }

    // Actualiza el progreso de la SubEtapa
    $subStageProgress = $this->obraStageSubStageService->updateSubStageProgress($task->obraStageSubStage);
    // Actualiza el proceso de la Etapa
    $stageProgress = $this->obraStageService->updateStageProgress($task->obraStageSubStage->obraStage);
    // Actualiza el progreso de la Obra
    $this->obraService->updateObraProgress($task->obraStageSubStage->obraStage->obra);

    $response = [
      'stageProgress' => $stageProgress,
      'subStageProgress' => $subStageProgress,
      'taskProgress' => (($task->progress_type === 'percentage') ? $task->progress : $task->current_quantity)
    ];

    return $response;
  }
}
