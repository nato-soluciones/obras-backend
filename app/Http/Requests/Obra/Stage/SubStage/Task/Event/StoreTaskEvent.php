<?php

namespace App\Http\Requests\Obra\Stage\SubStage\Task\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskEvent extends FormRequest
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
            'date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }
}
