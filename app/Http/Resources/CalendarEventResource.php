<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarEventResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'start_datetime' => $this->start_datetime?->toISOString(),
            'end_datetime' => $this->end_datetime?->toISOString(),
            'duration' => $this->duration,
            'location' => $this->location,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'notes' => $this->notes,
            'source' => $this->source,
            'meeting_link' => $this->meeting_link,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relaciones
            'organizer' => $this->whenLoaded('user', [
                'id' => $this->user?->id,
                'name' => $this->user?->firstname . ' ' . $this->user?->lastname,
                'email' => $this->user?->email,
            ]),
            
            'category' => $this->whenLoaded('category', new CalendarEventCategoryResource($this->category)),
            
            'participants' => $this->whenLoaded('participants', 
                CalendarEventParticipantResource::collection($this->participants)
            ),
            
            // Para FullCalendar
            'backgroundColor' => $this->category?->color,
            'borderColor' => $this->category?->color,
            'textColor' => '#ffffff',
            'allDay' => false,
        ];
    }
}
