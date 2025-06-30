<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class CreateMaterialRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'category_id' => 'required|exists:material_categories,id',
            'dimensions' => 'nullable|string|max:255',
            'quantity_per_package' => 'nullable|numeric',
            'color' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'measurement_unit_id' => 'required|exists:measurement_units,id',
        ];
    }
    
    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'category_id.required' => 'La categoría es obligatoria',
            'measurement_unit_id.required' => 'La unidad de medida es obligatoria',
        ];
    }
}
