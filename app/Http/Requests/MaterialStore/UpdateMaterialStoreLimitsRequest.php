<?php

namespace App\Http\Requests\MaterialStore;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialStoreLimitsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'minimum_limit' => 'required|numeric|min:0',
            'critical_limit' => 'required|numeric|min:0|lte:minimum_limit',
        ];
    }

    public function messages(): array
    {
        return [
            'critical_limit.lte' => 'El límite crítico debe ser menor o igual al límite mínimo',
        ];
    }
} 