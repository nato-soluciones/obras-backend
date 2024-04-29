<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->input('id');

        return [
            'email' => ['email', Rule::unique('users', 'email')->ignore($userId)],
        ];
    }

    public function messages()
    {
        return [
            'email' => 'El Email no tiene el formato correcto',
            'email.unique' => 'El Email ya está en uso, ingrese otro',
        ];
    }
}
