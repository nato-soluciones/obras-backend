<?php

namespace App\Http\Requests\Ipc;

use Illuminate\Foundation\Http\FormRequest;

class CreateIpcRequest extends FormRequest
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
            'period' => ['required', 'date', 'unique:ipc'],
            'value' => ['required', 'numeric'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'period.unique' => 'Este periodo ya fue registrado anteriormente.',
        ];
    }
}
