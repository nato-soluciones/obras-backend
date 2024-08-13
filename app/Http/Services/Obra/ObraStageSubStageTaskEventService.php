<?php

namespace App\Http\Services\Obra;

use App\Http\Requests\Obra\Stage\SubStage\Task\Detail\StoreTaskDetail;
use App\Http\Requests\Obra\Stage\SubStage\Task\Event\StoreTaskEvent;
use App\Models\Obra;
use App\Models\ObraStageSubStageTaskEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageSubStageTaskEventService
{
  private function validateRelationships(int $obraId, int $taskId)
  {
    $userId = auth()->user()->id;
    $ObraTaskExists = Obra::join('obra_stages', 'obras.id', '=', 'obra_stages.obra_id')
      ->join('obra_stage_sub_stages', 'obra_stages.id', '=', 'obra_stage_sub_stages.obra_stage_id')
      ->join('obra_stage_sub_stage_tasks', 'obra_stage_sub_stages.id', '=', 'obra_stage_sub_stage_tasks.obra_stage_sub_stage_id')
      ->where('obras.id', $obraId)
      ->where('obra_stage_sub_stage_tasks.id', $taskId)
      // ->where('obra_stage_sub_stage_tasks.responsible_id', $userId)
      ->exists();

    if (!$ObraTaskExists) {
      throw ValidationException::withMessages(['obra' => 'La tarea no existe o no tiene permisos para realizar esta acción.']);
    }

    return true;
  }

  public function index(int $obraId, int $taskId)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $taskId);

    $taskDetailEvents = ObraStageSubStageTaskEvent::where('obra_stage_sub_stage_task_id', $taskId)->get();

    return $taskDetailEvents;
  }

  public function store(StoreTaskEvent $request, int $obraId, int $taskId, ObraDailyLogService $obraDailyLogService)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $taskId);

    $today = date('Y-m-d');
    // Creamos la tarea
    $request->merge([
      'date' => $today,
      'obra_stage_sub_stage_task_id' => $taskId,
    ]);

    $comment = 'Nuevo evento en tarea ' . $taskId . "\n" . ' Titulo: ' . $request->title . "\n" . ' Descripción: ' . $request->description;

    $dailyLogRecord = [
      'event_date' => $today,
      'comment' => $comment,
      'obra_daily_log_tag_id' => 2,
      'obra_id' => $obraId,
    ];

    try {
      $response = DB::transaction(function () use ($request, $dailyLogRecord, $obraDailyLogService) {

        ObraStageSubStageTaskEvent::create($request->all());
        $obraDailyLogService->store(new Request($dailyLogRecord));

        return ['status' => 'ok'];
      });


      return $response;
    } catch (\Exception $e) {
      Log::error('Error en la transacción: ' . $e->getMessage());
      return $e;
    }
  }
}
