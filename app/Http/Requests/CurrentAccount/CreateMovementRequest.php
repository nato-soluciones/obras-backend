<?php

namespace App\Http\Requests\CurrentAccount;

use Illuminate\Foundation\Http\FormRequest;

class CreateMovementRequest extends FormRequest
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
            'movement_type_id' =>['required', 'numeric'],
            'description' => ['required'],
            'amount' => ['required', 'numeric'],
            'observation' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'movement_type_id' => 'El campo Tipo de movimiento no tiene el formato correcto.',
            'description' => 'El campo Detalle es obligatorio.',
            'amount' => 'El campo Monto es obligatorio.',
        ];
    }
}
