<?php

namespace App\Http\Requests\FleetMovement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFleetMovementRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'location' => ['required', 'string'],
            'type' => ['required', 'string', 'in:MAINTENANCE,REPAIR'],
            'responsible' => ['required', 'string'],
            'mileage' => ['required', 'integer'],
            'service' => ['required', 'string'],
            'new_image' => ['nullable', 'image'],
            'comments' => ['nullable', 'string'],
            'fleet_id' => ['required', 'exists:fleets,id'],
        ];
    }
}
