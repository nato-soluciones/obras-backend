<?php

namespace App\Http\Requests\Movement;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementOutputRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'created_by_id' => 'required|exists:users,id',
            'store_id' => 'required|exists:stores,id',
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|numeric|min:0.01',
            'store_movement_concept_id' => 'required|exists:store_movement_concepts,id',
        ];
    }
} 