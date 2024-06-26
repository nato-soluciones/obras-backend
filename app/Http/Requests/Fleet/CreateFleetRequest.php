<?php

namespace App\Http\Requests\Fleet;

use Illuminate\Foundation\Http\FormRequest;

class CreateFleetRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'brand' => ['required', 'string'],
            'model' => ['required', 'string'],
            'value' => ['nullable', 'numeric'],
            'initial_mileage' => ['required', 'numeric'],
            'domain' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'purchase_date' => ['required', 'date'],
            'vtv_expiration' => ['nullable', 'date'],
            'next_plate_payment' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:IN_USE,UNDER_REPAIR,DAMAGED,SOLD'],
            'type' => ['required', 'string', 'in:TRUCK,MOTORCYCLE,CAR,UTILITY,OTHER'],
        ];
    }
}
