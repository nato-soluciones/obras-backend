<?php

namespace App\Http\Controllers;

use App\Http\Requests\Obra\Stage\SubStage\Task\Event\StoreTaskEvent;
use App\Http\Services\Obra\ObraDailyLogService;
use App\Http\Services\Obra\ObraStageSubStageTaskEventService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageSubStageTaskEventController extends Controller
{
    private $oSSTaskEventService;

    public function __construct(ObraStageSubStageTaskEventService $obraStageSubStageTaskEventService)
    {
        $this->oSSTaskEventService = $obraStageSubStageTaskEventService;
    }

    public function index(int $obraId, int $taskId)
    {
        try {
        $taskDetailEvents = $this->oSSTaskEventService->index($obraId, $taskId);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al recuperar los eventos de la tarea'], 500);
        }
        return response($taskDetailEvents, 200);
    }

    public function store(StoreTaskEvent $request, int $obraId,  int $taskId, ObraDailyLogService $obraDailyLogService)
    {
        try {
            $taskDetailEvent = $this->oSSTaskEventService->store($request, $obraId, $taskId, $obraDailyLogService);
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
