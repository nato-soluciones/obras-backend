<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateToolRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:tools_categories,id'],
            'purchase_date' => ['nullable', 'date'],
            'status' => ['required', 'in:NEW,IN_USE,UNDER_REPAIR,DAMAGED,LOST'],
            'last_maintenance' => ['nullable', 'date'],

            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
        ];
    }
}
