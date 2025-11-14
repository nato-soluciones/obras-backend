<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
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
        return [
            'text' => 'required|string|max:500',
            'datetime' => 'required|date|after:now',
            'user_ids' => 'required|array|min:1|max:50',
            'user_ids.*' => 'required|exists:users,id|distinct',
            'priority' => 'sometimes|in:baja,media,alta',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'text.required' => 'El texto del recordatorio es obligatorio.',
            'text.max' => 'El texto no puede superar los 500 caracteres.',
            'datetime.required' => 'La fecha y hora es obligatoria.',
            'datetime.after' => 'La fecha debe ser futura.',
            'user_ids.required' => 'Debe especificar al menos un destinatario.',
            'user_ids.array' => 'Los destinatarios deben ser una lista.',
            'user_ids.min' => 'Debe especificar al menos un destinatario.',
            'user_ids.max' => 'No puede asignar el recordatorio a más de 50 usuarios.',
            'user_ids.*.exists' => 'Uno o más usuarios especificados no existen.',
            'user_ids.*.distinct' => 'Los destinatarios deben ser únicos.',
            'priority.in' => 'La prioridad debe ser: baja, media o alta.',
        ];
    }
}
