<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateClientRequest extends FormRequest
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
            'cuit'  => ['regex:/^([0-9]{11})$/', Rule::unique('clients', 'cuit')],
            'zip'   => ['nullable', 'regex:/^([0-9]{4}|[a-zA-Z]{1}[0-9]{4}[a-zA-Z]{3})$/'],
            'phone' => ['nullable', 'regex:/^(^[0-9\+]{7,20})$/'],
            'email' => 'nullable|email',
        ];
    }

    public function messages()
    {
        return [
            'email' => 'El E-Mail no tiene el formato correcto',
            'zip'   => 'El Código Postal tiene que ser de 4 números o 1 letra, 4 números y 3 letras.',
            'phone' => 'El Teléfono no tiene el formato correcto',
            'cuit'  => 'El CUIT tiene que ser de 11 números.',
            'cuit.unique'  => 'El CUIT ya está en uso, ingrese otro.',
        ];
    }
}
