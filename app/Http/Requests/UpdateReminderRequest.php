<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReminderRequest extends FormRequest
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
            'text' => 'sometimes|string|max:500',
            'datetime' => 'sometimes|date',
            // 'user_id' => 'sometimes|exists:users,id',
            'priority' => 'sometimes|in:baja,media,alta',
            'is_resolved' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'text.max' => 'El texto no puede superar los 500 caracteres.',
            'datetime.date' => 'Formato de fecha inválido.',
            // 'user_id.exists' => 'El usuario especificado no existe.',
            'priority.in' => 'La prioridad debe ser: baja, media o alta.',
            'is_resolved.boolean' => 'El estado de resolución debe ser verdadero o falso.',
        ];
    }
}
