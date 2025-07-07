<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCalendarEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'sometimes|required|date',
            'end_datetime' => 'sometimes|required|date|after:start_datetime',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|in:confirmed,cancelled,pending,rescheduled',
            'visibility' => 'sometimes|in:private,public',
            'notes' => 'nullable|string',
            'calendar_event_category_id' => 'sometimes|required|exists:calendar_event_categories,id',
            'source' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url|max:500',
            'participants' => 'nullable|array',
            'participants.*.user_id' => 'nullable|exists:users,id',
            'participants.*.phone' => 'nullable|string|max:20',
        ];

        // Validación condicional para participantes
        if ($this->has('participants') && is_array($this->participants)) {
            foreach ($this->participants as $index => $participant) {
                // Si no tiene user_id, name y email son obligatorios (participante externo)
                if (empty($participant['user_id'])) {
                    $rules["participants.{$index}.name"] = 'required|string|max:255';
                    $rules["participants.{$index}.email"] = 'required|email|max:255';
                } else {
                    // Si tiene user_id, name y email son opcionales (participante interno)
                    $rules["participants.{$index}.name"] = 'nullable|string|max:255';
                    $rules["participants.{$index}.email"] = 'nullable|email|max:255';
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título del evento es obligatorio.',
            'start_datetime.required' => 'La fecha y hora de inicio es obligatoria.',
            'end_datetime.required' => 'La fecha y hora de fin es obligatoria.',
            'end_datetime.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'visibility.in' => 'La visibilidad debe ser privada o pública.',
            'calendar_event_category_id.required' => 'La categoría del evento es obligatoria.',
            'calendar_event_category_id.exists' => 'La categoría seleccionada no es válida.',
            'participants.*.name.required' => 'El nombre es obligatorio para participantes externos.',
            'participants.*.email.required' => 'El email es obligatorio para participantes externos.',
            'participants.*.user_id.exists' => 'El usuario seleccionado no existe.',
            'meeting_link.url' => 'El enlace de reunión debe ser una URL válida.',
        ];
    }
}
