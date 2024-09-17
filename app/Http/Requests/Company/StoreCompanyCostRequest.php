<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyCostRequest extends FormRequest
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
            'registration_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'payment_status' => ['required', 'string', 'in:PENDING,PAID,PARTIALLY_PAID'],
            'payment_date' => ['nullable', 'date'],
            'category_id' => ['required', 'exists:company_cost_categories,id'],
            'responsible_id' => ['required', 'exists:users,id'],
        ];
    }
}
