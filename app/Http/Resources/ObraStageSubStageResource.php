<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObraStageSubStageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'progress' => $this->progress,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'stage_id' => $this->obra_stage_id,
            'tasks' => $this->tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start_date' => $task->start_date,
                    'end_date' => $task->end_date,
                    'progress' => $task->progress,
                    'progress_type' => $task->progress_type,
                    'max_quantity' => $task->max_quantity,
                    'current_quantity' => $task->current_quantity,
                    'is_completed' => $task->is_completed,
                    'description' => $task->description,
                    'responsible_id' => $task->responsible->id,
                    'responsible_firstname' => $task->responsible->firstname,
                    'responsible_lastname' => $task->responsible->lastname,
                    'responsible_deleted_at' => $task->responsible->deleted_at,
                    'task_events_count' => $task->task_events_count,
                    'has_quality_control' => $task->has_quality_control,
                    'quality_control_template_id' => $task->has_quality_control ? $task->qualityControls->first()->template_id ?? null : null,
                ];
            }),
        ];
    }
}
