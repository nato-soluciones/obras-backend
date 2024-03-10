<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNoteRequest extends FormRequest
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
     * Docs: https://laravel.com/docs/10.x/validation#available-validation-rules
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'color' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }
}
