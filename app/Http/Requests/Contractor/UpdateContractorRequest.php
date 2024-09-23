<?php

namespace App\Http\Requests\Contractor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContractorRequest extends FormRequest
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
        $contractorId = $this->input('id');

        return [
            'zip' => ['regex:/^([0-9]{4}|[a-zA-Z]{1}[0-9]{4}[a-zA-Z]{3})$/'],
            'phone' => ['nullable', 'regex:/^(|^[0-9\+]{1,20})$/'],
            'email' => 'nullable|email',
            'cuit' => ['regex:/^(|[0-9]{11})$/', Rule::unique('contractors', 'cuit')->ignore($contractorId)],
            'bank_account' => ['nullable', 'regex:/^[0-9\/ -]{0,22}$/'],
            'bank_cbu' => ['nullable', 'regex:/^[0-9]{22}$/'],
            'bank_alias' => ['nullable', 'regex:/^(|[a-zA-Z0-9.-]{6,20})$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'zip' => 'El campo Código Postal tiene que ser de 4 números o 1 letra, 4 números y 3 letras.',
            'phone' => 'El campo Teléfono no tiene el formato correcto',
            'cuit' => 'El campo CUIT tiene que ser de 11 números.',
            'cuit.unique'  => 'El CUIT ya está en uso, ingrese otro.',
            'bank_account' => 'El campo Nro de Cuenta puede contener solo números, /, - y espacios en blanco, y debe tener un máximo de 22 caracteres.',
            'bank_cbu' => 'El campo CBU/CVU tiene que ser de 22 números.',
            'bank_alias' => 'El campo Alias tiene que ser de 6 a 20 números y/o letras.',
        ];
    }
}
