<?php

namespace App\Http\Services;

use App\Models\Obra;
use App\Models\ObraStage;
use App\Models\ObraStageSubStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageService
{
  /**
   * Dependency injection
   */
  protected $obraService;

  public function __construct(ObraService $obraService)
  {
    $this->obraService = $obraService;
  }

  public function store(Request $request, int $obraId)
  {
    $obra = Obra::find($obraId);
    if (!$obra) {
      throw ValidationException::withMessages(['sub_stage' => 'La obra no existe.']);
    }

    try {
      $request->merge([
        'progress' => 0,
        'created_by_id' => auth()->user()->id
      ]);

      $obraStage = ObraStage::create($request->all());

      // Actualiza el porcentaje de la obra
      $this->obraService->updateObraProgress($obraStage->obra);

      return $obraStage;
    } catch (\Exception $e) {
      Log::error("Error al crear la etapa {$request->name}: " . $e->getMessage());
      return $e;
    }
  }


  public function update(Request $request, int $obraId, int $stageId)
  {
    $obraStage = ObraStage::where('obra_id', $obraId)->find($stageId);
    if (!$obraStage) {
      throw ValidationException::withMessages(['sub_stage' => 'La Etapa no existe.']);
    }

    // Obtiene el valor actual del campo obra_porcentaje, start_date y end_date
    $oldPercentage = $obraStage->obra_percentage;
    $oldStartDate = $obraStage->start_date;
    $oldEndDate = $obraStage->end_date;

    // Actualiza el registro
    $obraStage->update($request->all());

    // Verifica si el valor del campo obra_porcentaje ha cambiado
    if ($oldPercentage != $obraStage->obra_percentage) {
      // Actualiza el progreso de la obra
      $this->obraService->updateObraProgress($obraStage->obra);
    }

    // Verifica si el valor del campo start_date o end_date ha cambiado
    if ($oldStartDate != $obraStage->start_date || $oldEndDate != $obraStage->end_date) {
      // Actualiza las fechas de las sub-etapas
      $subStages = ObraStageSubStage::where('obra_stage_id', $obraStage->id)->get();

      foreach ($subStages as $subStage) {
        $subStage->start_date = $obraStage->start_date;
        $subStage->end_date = $obraStage->end_date;
        $subStage->save();
      }
    }

    return $obraStage;
  }


  public function destroy(int $obraId, int $stageId)
  {
    $obraStage = ObraStage::where('obra_id', $obraId)->find($stageId);
    if (!$obraStage) {
      throw ValidationException::withMessages(['sub_stage' => 'La Etapa no existe.']);
    }
    $obraStage->delete();

    // Actualiza el porcentaje de la obra
    $this->obraService->updateObraProgress($obraStage->obra);

    return true;
  }

  public function updateStageProgress(ObraStage $stage)
  {
    try {
      // Obtiene todas las sub-etapas del stage
      $subStages = ObraStageSubStage::where('obra_stage_id', $stage->id)->get();
      $subStageCount = $subStages->count();
      $totalProgress = 0;

      foreach ($subStages as $subStage) {
        $totalProgress += $subStage->progress;
      }

      // Actualiza el campo progress del stage
      $stage->progress = $subStageCount > 0 ? round(($totalProgress / $subStageCount), 2) : 0;
      $stage->save();

      return $stage->progress;
    } catch (\Exception $e) {
      Log::error("Error al actualizar el porcentaje de la etapa {$stage->name}: {$e->getMessage()}");
      throw ValidationException::withMessages(['message' => "Error al actualizar el porcentaje de la etapa {$stage->name}"]);
    }
  }
}
