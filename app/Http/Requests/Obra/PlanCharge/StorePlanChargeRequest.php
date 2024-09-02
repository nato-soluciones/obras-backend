<?php

namespace App\Http\Requests\Obra\PlanCharge;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanChargeRequest extends FormRequest
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
            'planChargeData' => ['required', 'array'],
            'planChargeData.financed_amount' => ['required', 'numeric', 'min:0'],
            'planChargeData.installment_count' => ['required', 'integer', 'min:1'],
            'planChargeData.installment_frequency' => ['required', 'in:DAILY,WEEKLY,BIWEEKLY,MONTHLY'],
            'planChargeData.installment_first_due_date' => ['required', 'date'],

            'installments' => ['required', 'array', 'min:1'],
            'installments.*.number' => ['required', 'integer', 'min:1'],
            'installments.*.concept' => ['required', 'string', 'max:255'],
            'installments.*.due_date' => ['required', 'date'],
            'installments.*.installment_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
