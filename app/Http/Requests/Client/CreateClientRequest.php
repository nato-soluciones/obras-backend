<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

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
            'cuit'  => ['regex:/^(|[0-9]{11})$/'],
            'zip'   => ['regex:/^([0-9]{4}|[a-zA-Z]{1}[0-9]{4}[a-zA-Z]{3})$/'],
            'phone' => ['regex:/^(|^[0-9\+]{1,20})$/'],
            'email' => 'nullable|email',
        ];
    }

    public function messages()
    {
        return [
            'email' => 'El campo E-Mail no tiene el formato correcto',
            'zip'   => 'El campo Código Postal tiene que ser de 4 números o 1 letra, 4 números y 3 letras.',
            'phone' => 'El campo Teléfono no tiene el formato correcto',
            'cuit'  => 'El campo CUIT tiene que ser de 11 números.',
        ];
    }
}
