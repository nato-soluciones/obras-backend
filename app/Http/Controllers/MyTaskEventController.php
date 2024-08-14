<?php

namespace App\Http\Controllers;

use App\Http\Requests\Obra\Stage\SubStage\Task\Event\StoreTaskEvent;
use App\Http\Services\Obra\ObraDailyLogService;
use App\Http\Services\Obra\ObraStageSubStageTaskEventService;
use App\Models\ObraStageSubStageTask;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MyTaskEventController extends Controller
{
    private $oSSTaskEventService;

    public function __construct(ObraStageSubStageTaskEventService $obraStageSubStageTaskEventService)
    {
        $this->oSSTaskEventService = $obraStageSubStageTaskEventService;
    }


    public function store(StoreTaskEvent $request, int $obraId,  ObraStageSubStageTask $task, ObraDailyLogService $obraDailyLogService)
    {
        try {
            $taskDetailEvent = $this->oSSTaskEventService->store($request, $obraId, $task, $obraDailyLogService);
            return response()->json($taskDetailEvent, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear el evento de la tarea'], 500);
        }

        return response($taskDetailEvent, 201);
    }
}
