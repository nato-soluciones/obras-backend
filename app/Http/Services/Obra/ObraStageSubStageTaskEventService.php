<?php

namespace App\Http\Services\Obra;

use App\Http\Requests\Obra\Stage\SubStage\Task\Event\StoreTaskEvent;
use App\Models\Obra;
use App\Models\ObraStageSubStageTask;
use App\Models\ObraStageSubStageTaskEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageSubStageTaskEventService
{
  private function validateRelationships(int $obraId, int $taskId, bool $onlyAuthUser = true)
  {
    $userId = $onlyAuthUser ? auth()->id() : null;

    $ObraTaskExists = Obra::join('obra_stages', 'obras.id', '=', 'obra_stages.obra_id')
      ->join('obra_stage_sub_stages', 'obra_stages.id', '=', 'obra_stage_sub_stages.obra_stage_id')
      ->join('obra_stage_sub_stage_tasks', 'obra_stage_sub_stages.id', '=', 'obra_stage_sub_stage_tasks.obra_stage_sub_stage_id')
      ->where('obras.id', $obraId)
      ->where('obra_stage_sub_stage_tasks.id', $taskId)
      ->when($onlyAuthUser, function ($query) use ($userId) {
        return $query->where('obra_stage_sub_stage_tasks.responsible_id', $userId);
      })
      ->exists();

    if (!$ObraTaskExists) {
      throw ValidationException::withMessages(['obra' => 'La tarea no existe o no tiene permisos para realizar esta acción.']);
    }

    return true;
  }

  public function index(int $obraId, int $taskId)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $taskId, false);

    $taskDetailEvents = ObraStageSubStageTaskEvent::with(['createdBy' => function ($q) {
      $q->select('id', 'firstname', 'lastname');
    }])
      ->where('obra_stage_sub_stage_task_id', $taskId)
      ->orderBy('id', 'desc')
      ->get();

    return $taskDetailEvents;
  }

  public function store(StoreTaskEvent $request, int $obraId, int $taskId, ObraDailyLogService $obraDailyLogService)
  {
    // Validamos las relaciones
    $this->validateRelationships($obraId, $taskId, true);
    $task  = ObraStageSubStageTask::find($taskId);
    // Creamos la tarea
    $request->merge([
      'date' => date('Y-m-d'),
      'created_by_id' => $task->responsible_id,
      'obra_stage_sub_stage_task_id' => $task->id,
    ]);

    $comment = 'Nuevo evento en tarea ' . $task->title . "\n" . strtoupper($request->title) . "\n" .  $request->description;
    $dailyLogTag = $obraDailyLogService->getDailyLogTagByName('Actualización');

    $dailyLogRecord = [
      'event_date' => date('Y-m-d H:i:s'),
      'comment' => $comment,
      'obra_daily_log_tag_id' => $dailyLogTag->id,
      'obra_id' => $obraId,
    ];

    try {
      $response = DB::transaction(function () use ($request, $dailyLogRecord, $obraDailyLogService) {

        ObraStageSubStageTaskEvent::create($request->all());

        // No envolvemos la llamada al servicio en un try-catch aquí
        $obraDailyLogService->store(new Request($dailyLogRecord));

        // Este return solo se ejecutará si no se lanzaron excepciones
        return ['status' => 'ok'];
      });

      return $response;
    } catch (\Exception $e) {
      Log::error("Error al crear el evento de la tarea = " . $e->getMessage());
      throw ValidationException::withMessages(['sub_stage' => 'Error al crear el evento de la tarea.']);
    }
  }
}
