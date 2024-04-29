<?php

namespace App\Services;

use App\Models\Obra;
use App\Models\ObraStage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\ErrorHandler\Debug;

class ObraService
{
  public function updateObraProgress(Obra $obra)
  {
    try {
      // Obtiene todas las stage de la obra
      $stages = ObraStage::where('obra_id', $obra->id)->get();

      // Calcula el porcentaje de la obra, calculando por cada etapa el progreso por el porcentaje que representa
      $progress = 0;
      foreach ($stages as $stage) {
        $progress += ($stage->progress / 100) * $stage->obra_percentage;
      }

      $obra->progress = round($progress, 2);
      $obra->save();
      return ['status' => 'success'];
    } catch (\Exception $e) {
      Log::error("Error al actualizar el porcentaje de la obra {$obra->name}: {$e->getMessage()}");
      return ['status' => 'error', 'message' => "Error al actualizar el porcentaje de la obra {$obra->name}"];
    }
  }
}
