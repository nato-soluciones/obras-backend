<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObraStageSubStageTaskEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "date" => $this->date,
            "title" => $this->title,
            "description" => $this->description,
            "obra_stage_sub_stage_task_id" => $this->obra_stage_sub_stage_task_id,
            'user_first_name' => $this->createdBy->firstname,
            'user_last_name' => $this->createdBy->lastname,
        ];
    }
}
