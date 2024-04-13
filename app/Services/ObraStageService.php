<?php

namespace App\Services;

use App\Models\ObraStage;
use App\Models\ObraStageTask;
use Illuminate\Support\Facades\Log;

class ObraStageService
{
  public function updateStageProgress(ObraStage $stage)
  {
    // Obtiene todas las tareas del stage
    $tasks = ObraStageTask::where('obra_stage_id', $stage->id)->get();

    // Cuenta el número de tareas completadas
    $completedTasksCount = $tasks->where('is_completed', true)->count();

    // Calcula el porcentaje de tareas completadas
    $totalTasksCount = $tasks->count();
    $progress = $totalTasksCount > 0 ?  round(($completedTasksCount / $totalTasksCount) * 100, 2) : 0;

    // Actualiza el campo progress del stage
    $stage->progress = $progress;
    $stage->save();
  }
}