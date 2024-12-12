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
            'from_store_id' => 'required|integer|exists:stores,id',
            'to_store_id' => 'required|integer|exists:stores,id',
            'material_id' => 'required|integer|exists:materials,id',
            'quantity' => 'required|numeric|min:1',
            'status' => 'required|string|in:pending,completed,canceled',
        ];
    }
}
