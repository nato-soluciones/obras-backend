<?php

namespace App\Http\Services;

use App\Http\Requests\Obra\Stage\SubStage\StoreSubStageRequest;
use App\Http\Requests\Obra\Stage\SubStage\UpdateSubStageRequest;
use App\Models\Obra;
use App\Models\ObraStage;
use App\Models\ObraStageSubStage;
use App\Models\ObraStageSubStageTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageSubStageService
{
  /**
   * Dependency injection
   */
  protected $obraService;
  protected $obraStageService;

  public function __construct(ObraService $obraService,  ObraStageService $obraStageService)
  {
    $this->obraService = $obraService;
    $this->obraStageService = $obraStageService;
  }

  private function validateRelationships(int $obraId, int $stageId)
  {
    $obra = Obra::with(['stages' => function ($query) use ($stageId) {
      $query->where('id', $stageId);
    }])->find($obraId);

    if (!$obra) {
      throw ValidationException::withMessages(['obra' => 'La obra no existe.']);
    }

    if (!$obra->stages->first()) {
      throw ValidationException::withMessages(['stage' => 'La etapa no existe o no pertenece a esta obra.']);
    }

    return true;
  }

  public function store(StoreSubStageRequest $request, int $obraId, int $stageId)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $stageId);

    // Recuperamos la etapa
    $stage = ObraStage::where('id', $stageId)->where('obra_id', $obraId)->first();

    // Creamos la tarea
    $request->merge([
      'obra_stage_id' => $stageId,
      'start_date' => $stage->start_date,
      'end_date' => $stage->end_date,
      'progress' => 0,
      'created_by_id' => auth()->user()->id
    ]);

    try {
      $response = DB::transaction(function () use ($request) {
        $obraStageSubStage = ObraStageSubStage::create($request->all());

        $this->obraStageService->updateStageProgress($obraStageSubStage->obraStage);
        $this->obraService->updateObraProgress($obraStageSubStage->obraStage->obra);

        return [
          'status' => 'ok',
          'stageProgress' => $obraStageSubStage->obraStage->progress
        ];
      });


      return $response;
    } catch (\Exception $e) {
      Log::error('Error en la transacción: ' . $e->getMessage());
      return $e;
    }
  }

  public function update(UpdateSubStageRequest $request, int $obraId, int $stageId, int $subStageId)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $stageId);

    $obraStageSubStage = ObraStageSubStage::where('obra_stage_id', $stageId)->find($subStageId);
    if (is_null($obraStageSubStage)) {
      throw ValidationException::withMessages(['sub_stage' => 'La sub-etapa no existe.']);
    }

    $obraStageSubStage->update($request->all());

    return $obraStageSubStage;
  }

  public function delete(int $obraId, int $stageId, int $subStageId)
  {
    $this->validateRelationships($obraId, $stageId);

    $obraStageSubStage = ObraStageSubStage::where('obra_stage_id', $stageId)->find($subStageId);
    if (is_null($obraStageSubStage)) {
      throw ValidationException::withMessages(['sub_stage' => 'La sub-etapa no existe.']);
    }

    try {
      $response = DB::transaction(function () use ($obraStageSubStage) {
        $obraStageSubStage->delete();
        $this->obraStageService->updateStageProgress($obraStageSubStage->obraStage);
        $this->obraService->updateObraProgress($obraStageSubStage->obraStage->obra);
        return [
          'stageProgress' => $obraStageSubStage->obraStage->progress
        ];
      });
      return $response;
    } catch (\Exception $e) {
      return $e;
    }

    return true;
  }

  public function updateSubStageProgress(ObraStageSubStage $subStage)
  {
    try{
      $taskList = ObraStageSubStageTask::where('obra_stage_sub_stage_id', $subStage->id)->get();
      $taskCount = $taskList->count();
      // Log::debug("Se actualizarán {$taskCount} tareas de la sub-etapa {$subStage->name}");
      $totalProgress = 0;
  
      foreach ($taskList as $task) {
        if ($task->progress_type === 'percentage') {
          $totalProgress += $task->progress;
        } else if($task->progress_type === 'quantity') {
          $totalProgress += round(($task->current_quantity * 100) / $task->max_quantity, 2);
        }
      }
      // Log::debug("El porcentaje total de la sub-etapa {$subStage->name} es {$totalProgress}");

      $subStage->progress = $taskCount > 0 ? round(($totalProgress / $taskCount), 2) : 0;
      // Log::debug("El porcentaje de la sub-etapa {$subStage->name} es {$subStage->progress}");
      $subStage->save();

      return $subStage->progress;

    }catch(\Exception $e){
      Log::error("Error al actualizar el porcentaje de la sub-etapa {$subStage->name}: {$e->getMessage()}");
      throw ValidationException::withMessages(['message' => "Error al actualizar el porcentaje de la sub-etapa {$subStage->name}"]);
    }
  }
}
