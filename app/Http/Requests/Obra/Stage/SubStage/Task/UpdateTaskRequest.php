<?php

namespace App\Http\Requests\Obra\Stage\SubStage\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
            'progress_type' => ['required', 'in:percentage,quantity'],
            'responsible_id' => ['required', 'exists:users,id'],
        ];

        if ($this->progress_type === 'quantity') {
            $rules['max_quantity'] = ['required', 'integer', 'min:1'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'El campo título es obligatorio',
            'start_date.required' => 'El campo fecha de inicio es obligatorio',
            'end_date.required' => 'El campo fecha de finalización es obligatorio',
            'end_date.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio',
            'progress_type.required' => 'El campo tipo de progreso es obligatorio',
            'responsible_id.required' => 'El campo responsable es obligatorio',
            'max_quantity.required' => 'El campo cantidad máxima es obligatorio',
            'max_quantity.integer' => 'El campo cantidad máxima debe ser un valor numérico',
            'max_quantity.min' => 'El campo cantidad máxima debe ser mayor o igual a 1',
            'description.string' => 'El campo descripción debe ser una cadena de texto',
        ];
    }
}
