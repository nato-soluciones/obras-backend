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
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string'],
            'progress_type' => ['required', 'in:percentage,quantity'],
            'responsible_id' => ['required', 'exists:users,id'],
        ];

        if ($this->progress_type === 'quantity') {
            $rules['max_quantity'] = ['required', 'integer', 'min:1'];
        } elseif ($this->progress_type === 'percentage') {
            $rules['progress'] = ['required', 'integer', 'min:0', 'max:100'];
        }

        return $rules;
    }
}
