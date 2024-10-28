<?php

namespace App\Http\Services;

use App\Http\Requests\Obra\Stage\SubStage\Task\StoreTaskRequest;
use App\Http\Requests\Obra\Stage\SubStage\Task\UpdateTaskRequest;
use App\Models\Obra;
use App\Models\ObraStageSubStageTask;
use App\Models\QualityControl\QualityControlItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraStageSubStageTaskService
{
	protected $obraStageSubStageService;
	protected $obraStageService;
	protected $obraService;
	protected $qualityControlService;

	public function __construct(ObraService $obraService,  ObraStageService $obraStageService, ObraStageSubStageService $obraStageSubStageService, QualityControlService $qualityControlService)
	{
		$this->obraStageSubStageService = $obraStageSubStageService;
		$this->obraStageService = $obraStageService;
		$this->obraService = $obraService;
		$this->qualityControlService = $qualityControlService;
	}

	private function validateRelationships(int $obraId, int $stageId, int $subStageId)
	{
		$obra = Obra::with(['stages' => function ($query) use ($stageId, $subStageId) {
			$query->where('id', $stageId)->with(['subStages' => function ($query) use ($subStageId) {
				$query->where('id', $subStageId);
			}]);
		}])->find($obraId);

		if (!$obra) {
			throw ValidationException::withMessages(['obra' => 'La obra no existe.']);
		}

		$stage = $obra->stages->first();
		if (!$stage) {
			throw ValidationException::withMessages(['stage' => 'La etapa no existe o no pertenece a esta obra.']);
		}

		if (!$stage->subStages->first()) {
			throw ValidationException::withMessages(['sub_stage' => 'La sub-etapa no existe o no pertenece a esta etapa.']);
		}

		return true;
	}

	public function store(StoreTaskRequest $request, int $obraId, int $stageId, int $subStageId)
	{
		// Validamos las relaciones
		$this->validateRelationships($obraId, $stageId, $subStageId);

		// Creamos la tarea
		$request->merge([
			'progress' => 0,
			'current_quantity' => 0,
			'obra_stage_id' => $stageId,
			'obra_stage_sub_stage_id' => $subStageId,
			'created_by_id' => auth()->user()->id
		]);
		try {
			$response = DB::transaction(function () use ($request) {
				$task = ObraStageSubStageTask::create($request->all());

				// Si tiene control de calidad, lo guarda
				if ($request->has_quality_control) {
					$this->qualityControlService->store($task, $request->quality_control_id);
				}

				// Actualiza el progreso de la SubEtapa
				$subStageProgress = $this->obraStageSubStageService->updateSubStageProgress($task->obraStageSubStage);
				// Actualiza el proceso de la Etapa
				$stageProgress = $this->obraStageService->updateStageProgress($task->obraStageSubStage->obraStage);
				// Actualiza el progreso de la Obra
				$this->obraService->updateObraProgress($task->obraStageSubStage->obraStage->obra);

				return [
					'stageProgress' => $stageProgress,
					'subStageProgress' => $subStageProgress,
				];
			});
			return $response;
		} catch (\Exception $e) {
			Log::error('TASK SERVICE - Error en la transacción: ' . $e->getMessage());
			throw new Exception("Error al crear la tarea.");
		}
	}

	public function update(UpdateTaskRequest $request, int $obraId, int $stageId, int $subStageId, int $taskId)
	{
		// Validamos las relaciones
		$this->validateRelationships($obraId, $stageId, $subStageId);

		$obraStageSubStageTask = ObraStageSubStageTask::where('id', $taskId)
			->where('obra_stage_id', $stageId)
			->where('obra_stage_sub_stage_id', $subStageId)
			->firstOrFail();

		$obraStageSubStageTask->update($request->all());

		return $obraStageSubStageTask;
	}

	public function delete(int $obraId, int $stageId, int $subStageId, int $taskId)
	{
		$this->validateRelationships($obraId, $stageId, $subStageId);

		$task = ObraStageSubStageTask::where('id', $taskId)
			->where('obra_stage_id', $stageId)
			->where('obra_stage_sub_stage_id', $subStageId)
			->firstOrFail();

		try {
			$response = DB::transaction(function () use ($task) {
				// Eliminar el control de calidad y sus ítems asociados
				$qualityControls = $task->qualityControls ?? [];
				foreach ($qualityControls as $control) {
					$control->items()->delete();
					$control->delete();
				}

				$task->delete();

				// Actualiza el progreso de la SubEtapa
				$subStageProgress = $this->obraStageSubStageService->updateSubStageProgress($task->obraStageSubStage);
				// Actualiza el proceso de la Etapa
				$stageProgress = $this->obraStageService->updateStageProgress($task->obraStageSubStage->obraStage);
				// Actualiza el progreso de la Obra
				$this->obraService->updateObraProgress($task->obraStageSubStage->obraStage->obra);

				return [
					'stageProgress' => $stageProgress,
					'subStageProgress' => $subStageProgress,
				];
			});

			return $response;
		} catch (\Exception $e) {
			Log::error('Error en la transacción: ' . $e->getMessage());
			return $e;
		}
	}

	public function updateProgress(Request $request, int $obraId, int $stageId, int $subStageId, int $taskId)
	{
		// Validamos las relaciones
		$this->validateRelationships($obraId, $stageId, $subStageId);

		$task = ObraStageSubStageTask::where('id', $taskId)
			->where('obra_stage_id', $stageId)
			->where('obra_stage_sub_stage_id', $subStageId)
			->firstOrFail();

		if ($task->progress_type === 'percentage') {
			if ($request->progress < 0 || $request->progress > 100) {
				throw ValidationException::withMessages(['progress' => 'El avance debe ser entre 0 y 100.']);
			}
			if ($request->progress < $task->progress) {
				throw ValidationException::withMessages(['current_quantity' => "El avance ({$request->progress}) debe ser mayor al valor anterior ({$task->progress})."]);
			}

			$taskUpdate = [
				'progress' => $request->progress
			];

			if (intval($request->progress) === 100) {
				$taskUpdate['is_completed'] = true;
			}
			$task->update($taskUpdate);
		} else if ($task->progress_type === 'quantity') {
			if ($request->progress <= 0) {
				throw ValidationException::withMessages(['current_quantity' => 'El avance debe ser mayor a 0 (cero).']);
			}
			// }
			if ($request->progress > $task->max_quantity) {
				throw ValidationException::withMessages(['current_quantity' => "El avance debe ser menor o igual a {$task->max_quantity} (cantidad máxima)."]);
			}
			if ($request->progress < $task->current_quantity) {
				throw ValidationException::withMessages(['current_quantity' => "El avance ({$request->progress}) debe ser mayor al valor anterior ({$task->current_quantity})."]);
			}

			$taskUpdate = [
				'current_quantity' => $request->progress
			];

			if (intval($request->progress) === intval($task->max_quantity)) {
				$taskUpdate['is_completed'] = true;
			}

			$task->update($taskUpdate);
		}

		// Actualiza el progreso de la SubEtapa
		$subStageProgress = $this->obraStageSubStageService->updateSubStageProgress($task->obraStageSubStage);
		// Actualiza el proceso de la Etapa
		$stageProgress = $this->obraStageService->updateStageProgress($task->obraStageSubStage->obraStage);
		// Actualiza el progreso de la Obra
		$this->obraService->updateObraProgress($task->obraStageSubStage->obraStage->obra);

		$response = [
			'stageProgress' => $stageProgress,
			'subStageProgress' => $subStageProgress,
			'taskProgress' => (($task->progress_type === 'percentage') ? $task->progress : $task->current_quantity)
		];

		return $response;
	}

	public function updateProgressBulk(Request $request, int $obraId)
	{
		$taskList = $request->all();
		// Validamos la existencia de las tareas en la obra
		$taskIds = array_column($taskList, 'id');

		$ObraTasksValid = Obra::select('obras.id as obraId', 'obra_stage_sub_stage_tasks.id as taskId')
			->join('obra_stages', 'obras.id', '=', 'obra_stages.obra_id')
			->join('obra_stage_sub_stages', 'obra_stages.id', '=', 'obra_stage_sub_stages.obra_stage_id')
			->join('obra_stage_sub_stage_tasks', 'obra_stage_sub_stages.id', '=', 'obra_stage_sub_stage_tasks.obra_stage_sub_stage_id')
			->where('obras.id', $obraId)
			->whereIn('obra_stage_sub_stage_tasks.id', $taskIds)
			->get();

		// Obtener los IDs de las tareas que se encontraron en la consulta
		$foundTaskIds = $ObraTasksValid->pluck('taskId')->toArray();
		// Encuentra las tareas que faltan.
		$missingTaskIds = array_diff($taskIds, $foundTaskIds);

		if (!empty($missingTaskIds)) {
			$taskTitles = array_column($taskList, 'title', 'id');
			$missingTasksInfo = array_map(
				function ($id) use ($taskTitles) {
					return "{$taskTitles[$id]}";
				},
				$missingTaskIds
			);
			$errorMessage = "Las siguientes tareas no se encontraron en la obra: " . implode(', ', $missingTasksInfo);
			throw ValidationException::withMessages(['tasks' => $errorMessage]);
		}

		// Recorre las tareas y actualiza el progreso
		DB::transaction(function () use ($taskList) {
			foreach ($taskList as $task) {
				$taskDb = ObraStageSubStageTask::find($task['id']);
				if ($taskDb->progress_type === 'percentage') {
					if ($task['progress'] < 0 || $task['progress'] > 100) {
						throw ValidationException::withMessages(['progress' => 'El avance debe ser entre 0 y 100.']);
					}
					if ($task['progress'] < $taskDb->progress) {
						throw ValidationException::withMessages(['current_quantity' => "El avance ({$task['progress']}) debe ser mayor al valor anterior ({$taskDb->progress})."]);
					}

					$taskUpdate = [
						'progress' => $task['progress']
					];

					if ($task['progress'] === 100) {
						$taskUpdate['is_completed'] = true;
					}

					$taskDb->update($taskUpdate);
				} else if ($taskDb->progress_type === 'quantity') {
					if (!is_int($task['progress'])) {
						throw ValidationException::withMessages(['current_quantity' => 'El avance debe ser un valor entero.']);
					}
					if ($task['progress'] < 0 || $task['progress'] > $taskDb->max_quantity) {
						throw ValidationException::withMessages(['current_quantity' => 'El avance debe ser entre 0 y la cantidad máxima.']);
					}
					if ($task['progress'] < $taskDb->current_quantity) {
						throw ValidationException::withMessages(['current_quantity' => "El avance ({$task['progress']}) debe ser mayor al valor anterior ({$taskDb->current_quantity})."]);
					}

					$taskUpdate = [
						'current_quantity' => $task['progress']
					];

					if ($task['progress'] === $taskDb->max_quantity) {
						$taskUpdate['is_completed'] = true;
					}

					$taskDb->update($taskUpdate);
				}

				// Actualiza el progreso de la SubEtapa
				$this->obraStageSubStageService->updateSubStageProgress($taskDb->obraStageSubStage);
				// Actualiza el proceso de la Etapa
				$this->obraStageService->updateStageProgress($taskDb->obraStageSubStage->obraStage);
				// Actualiza el progreso de la Obra
				$this->obraService->updateObraProgress($taskDb->obraStageSubStage->obraStage->obra);
			}
		});

		return true;
	}

	public function getQualityControl(int $obraId, int $stageId, int $subStageId, int $taskId)
	{
		// Validamos las relaciones
		$this->validateRelationships($obraId, $stageId, $subStageId);

		$task = ObraStageSubStageTask::with([
			'qualityControls.items' => function ($query) {
				$query->orderBy('id', 'asc');
			},
			'qualityControls.template',
			'qualityControls.items.templateItem'
		])->find($taskId);

		if (!$task) {
			throw ValidationException::withMessages(['task' => 'La tarea no existe.']);
		}

		if (!$task->has_quality_control) {
			return [];
		}

		// Estructura la respuesta aplanada
		return [
			'id' => $task->qualityControls->id,
			'template_id' => $task->qualityControls->template_id,
			'name' => $task->qualityControls->template->name,
			'status' => $task->qualityControls->status,
			'percentage' => $task->qualityControls->percentage,
			'comments' => $task->qualityControls->comments,
			'items' => $task->qualityControls->items->map(function ($item) {
				return [
					'id' => $item->id,
					'template_item_id' => $item->template_item_id,
					'name' => $item->templateItem->name,
					'passed' => $item->passed,
				];
			}),
		];


		return $qualityControls;
	}

	public function updateQualityControl(Request $request, int $obraId, int $stageId, int $subStageId, int $taskId)
	{
		// Validamos las relaciones
		$this->validateRelationships($obraId, $stageId, $subStageId);

		$task = ObraStageSubStageTask::find($taskId);

		if (!$task) {
			throw ValidationException::withMessages(['task' => 'La tarea no existe.']);
		}

		if (!$task->has_quality_control) {
			throw ValidationException::withMessages(['task' => 'La tarea no tiene control de Calidad.']);
		}

		$qualityControl = $task->qualityControls;
		if (!$qualityControl || $qualityControl->id !== $request->id) {
			throw ValidationException::withMessages(['quality_control' => 'El control de calidad no existe.']);
		}

		$totalItems = count($qualityControl->items);
		$passedItems = 0;

		// Actualizar cada item de control de calidad
		foreach ($request->items as $itemData) {
			$item = QualityControlItem::where('id', $itemData['id'])
				->where('quality_control_id', $qualityControl->id)
				->first();

			if ($item) {
				$item->passed = $itemData['passed'];
				$item->save();

				if ($item->passed) {
					$passedItems++;
				}
			}
		}
		$percentage = $totalItems > 0 ? round(($passedItems / $totalItems) * 100, 2) : 0;
		Log::debug("Porcentaje {$percentage} Total Items {$totalItems} Passed Items {$passedItems}");

		$qualityControl->percentage = $percentage;
		$qualityControl->status = $percentage < 100 ? 'CONTROLLED_WITH_ERRORS' : 'CONTROLLED_OK';
		$qualityControl->comments = $request->comments ?? null;
		$qualityControl->save();

		return true;
	}
}
