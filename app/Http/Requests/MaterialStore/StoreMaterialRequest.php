<?php

namespace App\Http\Requests\MaterialStore;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'material_id' => 'required|exists:materials,id',
            'store_id' => 'required|exists:stores,id',
            'quantity' => 'required|numeric',
            'minimum_limit' => 'required|numeric',
            'critical_limit' => 'required|numeric',
        ];
    }
}
