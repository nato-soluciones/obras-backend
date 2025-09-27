<?php

namespace App\Http\Resources\Reminders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResourceCollection extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'datetime' => $this->datetime,
            'priority' => $this->priority,
            'is_resolved' => $this->is_resolved,
            'date_resolved' => $this->date_resolved,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->full_name ?? null,
            ],
            'creator' => [
                'id' => $this->creator->id ?? null,
                'name' => $this->creator->full_name ?? null,
            ],
            'is_assigned_by_other' => $this->user_id !== $this->created_by,
        ];
    }
}
