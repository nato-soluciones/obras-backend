<?php

namespace App\Http\Requests\Movement;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
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
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'store_movement_concept_id' => 'required|exists:store_movement_concepts,id',
        ];
    }

    public function messages(): array
    {
        return [
            'to_store_id.different' => 'El almacén de destino debe ser diferente al almacén de origen',
            'quantity.min' => 'La cantidad debe ser mayor a 0',
        ];
    }
}
