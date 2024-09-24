<?php

namespace App\Http\Controllers;

use App\Http\Services\ObraStageSubStageTaskService;
use App\Models\Obra;
use App\Models\ObraStageSubStageTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MyTaskController extends Controller
{
    public function obrasList(Request $request)
    {
        $user_id = auth()->id();
        $tasksPending = $request->input('tasksPending');
        $perPage = 10;
        $page = $request->input('page', 1);

        $obras = Obra::distinct()
            ->join('obra_stages as os', 'obras.id', '=', 'os.obra_id')
            ->join('obra_stage_sub_stages as oss', 'os.id', '=', 'oss.obra_stage_id')
            ->join('obra_stage_sub_stage_tasks as osst', 'oss.id', '=', 'osst.obra_stage_sub_stage_id')
            ->where('osst.responsible_id', $user_id)
            ->when($tasksPending, function ($query) use ($tasksPending) {
                if ($tasksPending === 'WITH') {
                    $query->where('osst.is_completed', false);
                } elseif ($tasksPending === 'WITHOUT') {
                    $query->where('osst.is_completed', true);
                }
            })
            ->select('obras.id', 'obras.name', 'obras.image', 'obras.address', 'obras.phone', 'obras.start_date', 'obras.end_date', 'obras.status', 'obras.progress')
            ->get();

        $obrasConTareasPendientes = $obras->map(function ($obra) use ($user_id, $tasksPending) {
            $tareasPendientesCount = ObraStageSubStageTask::whereHas('obraStageSubStage.obraStage', function ($query) use ($obra) {
                $query->where('obra_id', $obra->id);
            })
                ->where('is_completed', false)
                ->where('responsible_id', $user_id)
                ->count();

            if ($tasksPending === 'WITHOUT' && $tareasPendientesCount > 0) {
                return null;
            }
            // Agrega el conteo de tareas pendientes a cada obra
            $obra->tasks_pending = $tareasPendientesCount;

            return $obra;
        })->filter()->values(); // Elimina las entradas nulas del resultado

        // Paginar manualmente la colección filtrada
        $total = $obrasConTareasPendientes->count();
        $startingPoint = ($page - 1) * $perPage;
        $itemsForCurrentPage = $obrasConTareasPendientes->slice($startingPoint, $perPage)->values();

        // Crear la estructura de paginación manualmente
        $paginatedData = [
            'data' => $itemsForCurrentPage,
            'current_page' => intval($page),
            'last_page' => ceil($total / $perPage),
        ];

        return response($paginatedData, 200);
    }

    public function myTasksInObra(int $obraId)
    {
        $user_id = auth()->id();

        $tasks = ObraStageSubStageTask::select(
            'obra_stage_sub_stage_tasks.*',
            'obra_stage_sub_stages.id as sub_stage_id',
            'obra_stage_sub_stages.name as sub_stage_name',
            'obra_stages.id as stage_id',
            'obra_stages.name as stage_name'
        )
            ->join('obra_stage_sub_stages', 'obra_stage_sub_stage_tasks.obra_stage_sub_stage_id', '=', 'obra_stage_sub_stages.id')
            ->join('obra_stages', 'obra_stage_sub_stages.obra_stage_id', '=', 'obra_stages.id')
            ->where('obra_stages.obra_id', $obraId)
            ->where('obra_stage_sub_stage_tasks.responsible_id', $user_id)
            ->withCount('taskEvents')
            ->get();

        // $groupedTasks = $tasks->groupBy(['stage_name', 'sub_stage_name']);
        $groupedTasks = $tasks->groupBy('stage_id')->map(function ($stageTasks) {
            return [
                'stage_id' => $stageTasks->first()->stage_id,
                'stage_name' => $stageTasks->first()->stage_name,
                'sub_stages' => $stageTasks->groupBy('sub_stage_id')->map(function ($subStageTasks) {
                    return [
                        'sub_stage_id' => $subStageTasks->first()->sub_stage_id,
                        'sub_stage_name' => $subStageTasks->first()->sub_stage_name,
                        'tasks' => $subStageTasks
                    ];
                })->values()
            ];
        })->values();
        return response($groupedTasks, 200);
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
