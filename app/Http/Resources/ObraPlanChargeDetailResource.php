<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\Obra as EnumsObra;

class ObraPlanChargeDetailResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			"id" => $this->id,
			"obra_plan_charge_id" => $this->obra_plan_charge_id,
			"type" => $this->type,
			"installment_number" => $this->installment_number,
			"concept" => $this->concept,
			"due_date" => $this->due_date,
			"installment_amount" => $this->installment_amount,
			"adjustment_amount" => $this->adjustment_amount,
			"total_amount" => $this->total_amount,
			"status" => $this->status,
			"status_text" => EnumsObra::$paymentState[$this->status],

		];
	}
}
