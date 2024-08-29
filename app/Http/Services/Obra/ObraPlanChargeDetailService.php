<?php

namespace App\Http\Services\Obra;

use App\Models\Obra\ObraPlanCharge;
use App\Models\Obra\ObraPlanChargeDetail;
use App\Models\Obra\ObraPlanChargeDetailPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraPlanChargeDetailService
{

	public function store(Request $request, int $obraId)
	{
		try {
			$obraPlanCharge = ObraPlanCharge::where('obra_id', $obraId)->first();

			if (!$obraPlanCharge) {
				throw ValidationException::withMessages(['obra_plan_charge' => 'Plan de cobro no encontrado.']);
			}

			$request->merge([
				'obra_plan_charge_id' => $obraPlanCharge->id,
				'total_amount' => $request->installment_amount,
				'status' => 'PENDING',
			]);

			$obraPlanChargeDetail = ObraPlanChargeDetail::create($request->all());
			return $obraPlanChargeDetail;
		} catch (\Exception $e) {
			Log::error("Error al crear el detalle del plan de cobro, obraId {$obraId}:" . $e->getMessage());
			throw ValidationException::withMessages(['obra' => "Error al crear el detalle del plan de cobro, obraId {$obraId}"]);
		}
	}

	public function charge(Request $request, int $obraId, int $detailId)
	{
		try {
			// Valida que el detalle exista en la obra
			$obraPlanChargeDetail = ObraPlanChargeDetail::with(['planCharge' => function ($q) use ($obraId) {
				$q->where('obra_id', $obraId);
			}])->where('id', $detailId)->first();

			if (!$obraPlanChargeDetail) {
				throw ValidationException::withMessages(['obra_plan_charge' => 'Plan de cobro no encontrado.']);
			}

			// Recupera el monto total pagado
			$sumPayment = ObraPlanChargeDetailPayment::where('obra_plan_charge_detail_id', $detailId)->sum('amount');

			$today = Carbon::now()->format('Y-m-d');
			DB::beginTransaction();

			////////////////////////////////////////////////
			// Actualiza PlanChargeDetail con el monto pagado
			////////////////////////////////////////////////
			$chargeDetailUpdate = [];

			// Suma todos los pagos realizados incluido el actual
			$totalPayment = $request->payment_amount + $sumPayment;

			// Inicializa los valores a guardar
			$chargeDetailUpdate['total_amount'] = $obraPlanChargeDetail->total_amount;
			$chargeDetailUpdate['status'] = 'PARTIALLY_PAID';

			// Si es el primer pago y tiene ajuste, calcula el ajuste y actualiza valores
			if ($sumPayment === 0 && $request->index_type) {
				$chargeDetailUpdate['index_type'] = $request->index_type;
				$chargeDetailUpdate['index_period'] = $request->index_period;
				// $pcDetailUpdate['adjustment_amount'] = 0; // calcular el ajuste nuevamente
				$chargeDetailUpdate['total_amount'] = $obraPlanChargeDetail->installment_amount + ($chargeDetailUpdate['adjustment_amount'] ?? 0);
			}

			// Si el monto total pagado es mayor o igual al monto total de la cuota, se marca como pagado
			if ($totalPayment >= $chargeDetailUpdate['total_amount']) {
				$chargeDetailUpdate['full_payment_date'] = $today;
				$chargeDetailUpdate['status'] = 'PAID';
			}

			// Log::info($chargeDetailUpdate);
			$obraPlanChargeDetail->update($chargeDetailUpdate);

			////////////////////////
			// Registrar el pago realizado
			////////////////////////
			$payment = [
				'obra_plan_charge_detail_id' => $detailId,
				'date' => $today,
				'amount' => $request->payment_amount,
				'description' => !empty($request->payment_description) ? $request->payment_description : null,
				'created_by_id' => auth()->id(),
			];
			ObraPlanChargeDetailPayment::create($payment);

			// registrar el ingreso

			// registrar en cuenta corriente del cliente


			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Error al registrar el pago, obraId {$obraId} DetalleId {$detailId}: " . $e->getMessage());
			throw ValidationException::withMessages(['obra' => "Error al registrar el pago, obraId {$obraId}"]);
		}
	}
}
