<?php

namespace App\Http\Controllers;

use App\Http\Services\Obra\ObraStageSubStageTaskEventService;
use App\Http\Services\ObraStageSubStageTaskService;
use App\Models\Obra;
use App\Models\ObraStageSubStageTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MyTaskController extends Controller
{
    public function obrasList()
    {
        $user_id = auth()->id();

        $obras = Obra::distinct()
            ->join('obra_stages as os', 'obras.id', '=', 'os.obra_id')
            ->join('obra_stage_sub_stages as oss', 'os.id', '=', 'oss.obra_stage_id')
            ->join('obra_stage_sub_stage_tasks as osst', 'oss.id', '=', 'osst.obra_stage_sub_stage_id')
            ->where('osst.responsible_id', $user_id)
            ->where('osst.is_completed', '=', false)
            ->select('obras.id', 'obras.name', 'obras.image', 'obras.address', 'obras.phone', 'obras.start_date', 'obras.end_date', 'obras.status', 'obras.progress')
            ->get();

        $obrasConTareasPendientes = $obras->map(function ($obra) use ($user_id) {
            $tareasPendientesCount = ObraStageSubStageTask::whereHas('obraStageSubStage.obraStage', function ($query) use ($obra) {
                $query->where('obra_id', $obra->id);
            })
                ->where('is_completed', false)
                ->where('responsible_id', $user_id)
                ->count();

            // Agrega el conteo de tareas pendientes a cada obra
            $obra->tasks_pending = $tareasPendientesCount;

            return $obra;
        });

        return response($obrasConTareasPendientes, 200);
    }

    public function myTasksInObra(int $obraId)
    {
        $user_id = auth()->id();

        $tasks = ObraStageSubStageTask::select(
            'obra_stage_sub_stage_tasks.*',
            'obra_stage_sub_stages.id as sub_stage_id',
            'obra_stages.id as stage_id'
        )
            ->join('obra_stage_sub_stages', 'obra_stage_sub_stage_tasks.obra_stage_sub_stage_id', '=', 'obra_stage_sub_stages.id')
            ->join('obra_stages', 'obra_stage_sub_stages.obra_stage_id', '=', 'obra_stages.id')
            ->where('obra_stages.obra_id', $obraId)
            ->where('obra_stage_sub_stage_tasks.responsible_id', $user_id)
            ->withCount('taskEvents')
            ->get();

        return response($tasks, 200);
    }

    public function bulkUpdate(Request $request, int $obraId, ObraStageSubStageTaskService $obraStageSubStageTaskService)
    {
        try {
            $obraStageSubStageTaskService->updateProgressBulk($request, $obraId);
            return response()->json(['message' => 'Tareas guardadas exitosamente.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar las tareas'], 500);
        }
    }
}
