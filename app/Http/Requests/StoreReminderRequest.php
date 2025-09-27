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
            'user_id' => 'required|exists:users,id',
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
            'user_id.required' => 'Debe especificar a quién asignar el recordatorio.',
            'user_id.exists' => 'El usuario especificado no existe.',
            'priority.in' => 'La prioridad debe ser: baja, media o alta.',
        ];
    }
}
