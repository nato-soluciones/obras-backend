<?php

namespace App\Http\Controllers;

use App\Http\Resources\ObraStageSubStageTaskEventResource;
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

    public function index(int $obraId, int $stageId, int $subStageId, int $taskId)
    {
        try {
            $taskDetailEvents = $this->oSSTaskEventService->index($obraId, $taskId);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al recuperar los eventos de la tarea'], 500);
        }
        return ObraStageSubStageTaskEventResource::collection($taskDetailEvents);
    }
}
