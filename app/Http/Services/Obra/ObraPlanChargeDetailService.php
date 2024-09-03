<?php

namespace App\Http\Services\Obra;

use App\Models\Auxiliaries\IndexType;
use App\Models\Cac;
use App\Models\CurrentAccountMovementType;
use App\Models\Income;
use App\Models\Ipc;
use App\Models\Obra;
use App\Models\Obra\ObraPlanCharge;
use App\Models\Obra\ObraPlanChargeDetail;
use App\Models\Obra\ObraPlanChargeDetailPayment;
use App\Services\CurrentAccountService;
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
			$obra = Obra::findOrFail($obraId);
			$obraPlanCharge = ObraPlanCharge::where('obra_id', $obraId)->first();

			if (!$obraPlanCharge) {
				throw ValidationException::withMessages(['obra_plan_charge' => 'Plan de cobro no encontrado.']);
			}

			$today = Carbon::now()->format('Y-m-d');

			DB::beginTransaction();
			$dataDetail = [
				'obra_plan_charge_id' => $obraPlanCharge->id,
				'total_amount' => $request->installment_amount,
				'status' => 'PENDING',
			];

			if ($request->type === 'ADJUSTMENT' && $request->installment_amount < 0) {
				$dataDetail['status'] = 'PAID';
				$dataDetail['full_payment_date'] = $today;
			}

			$request->merge($dataDetail);
			$obraPlanChargeDetail = ObraPlanChargeDetail::create($request->all());

			// Crea el ajuste en CC si es necesario
			if ($request->type === 'ADJUSTMENT') {
				$CAService = app(CurrentAccountService::class);
				$CA_Client = [
					'project_id' => $obra->id,
					'entity_type' => 'CLIENT',
					'entity_id' => $obra->client_id,
					'currency' => $obra->currency,
				];
				if ($request->installment_amount > 0) {
					$movementType = CurrentAccountMovementType::select('id')
						->where('entity_type', 'CLIENT')
						->where('name', 'Ajustes')
						->first();
				} else {
					$movementType = CurrentAccountMovementType::select('id')
						->where('entity_type', 'CLIENT')
						->where('name', 'Descuentos o Bonificaciones')
						->first();
				}

				$CA_movement = [
					'date' => $today,
					'movement_type_id' => $movementType->id,
					'description' => $obraPlanChargeDetail->concept,
					'amount' => abs($obraPlanChargeDetail->installment_amount),
					'observation' => $obraPlanChargeDetail->description,
					'reference_entity' => 'planChargeDetail',
					'reference_id' => $obraPlanChargeDetail->id,
					'created_by' => auth()->id()
				];
				$CAService->CAMovementAdd($CA_Client, $CA_movement);
			}


			DB::commit();
			return $obraPlanChargeDetail;
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Error al crear el detalle del plan de cobro, obraId {$obraId}:" . $e->getMessage());
			throw ValidationException::withMessages(['obra' => "Error al crear el detalle del plan de cobro, obraId {$obraId}"]);
		}
	}

	public function charge(Request $request, int $obraId, int $detailId)
	{
		try {
			$obra = Obra::findOrFail($obraId);

			// Valida que el detalle exista en la obra
			$obraPlanChargeDetail = ObraPlanChargeDetail::with(['planCharge' => function ($q) use ($obraId) {
				$q->where('obra_id', $obraId);
			}])->where('id', $detailId)->first();

			if (!$obraPlanChargeDetail) {
				throw ValidationException::withMessages(['obra_plan_charge' => 'Plan de cobro no encontrado.']);
			}

			// Si envía el indice, verifica que exista
			$indexTypeId = null;
			if ($request->index_type) {
				$indexType = IndexType::where('code', $request->index_type)->first();
				if (!$indexType) {
					throw ValidationException::withMessages(['index' => 'El indice no existe.']);
				}

				$indexTypeId = $indexType->id;
				$indexAdjustmentAmount = $this->getAdjustmentAmount($obraId, $obraPlanChargeDetail->installment_amount, $request->index_type, $request->index_period);
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
			if ($sumPayment === 0 && $indexTypeId) {
				$chargeDetailUpdate['index_type'] 	 = $indexTypeId;
				$chargeDetailUpdate['index_period']  = $request->index_period;
				$chargeDetailUpdate['adjustment_amount'] = $indexAdjustmentAmount;
				$chargeDetailUpdate['total_amount']  = $obraPlanChargeDetail->installment_amount + ($indexAdjustmentAmount ?? 0);
			}

			// Si el monto total pagado es mayor o igual al monto total de la cuota, se marca como pagado
			if ($totalPayment >= $chargeDetailUpdate['total_amount']) {
				$chargeDetailUpdate['full_payment_date'] = $today;
				$chargeDetailUpdate['status'] = 'PAID';
			}

			// Log::info($chargeDetailUpdate);
			$obraPlanChargeDetail->update($chargeDetailUpdate);

			////////////////////////////////////////////////
			// Registrar el pago realizado
			////////////////////////////////////////////////
			$payment = [
				'obra_plan_charge_detail_id' => $detailId,
				'date' => $today,
				'amount' => $request->payment_amount,
				'description' => !empty($request->payment_description) ? $request->payment_description : null,
				'created_by_id' => auth()->id(),
			];
			ObraPlanChargeDetailPayment::create($payment);

			////////////////////////////////////////////////
			// Registra el ingreso
			////////////////////////////////////////////////
			$statusPayment = $chargeDetailUpdate['status'] === 'PAID' ? 'Cobro Total' : 'Cobro Parcial';
			$income = [
				'obra_id' => $obraId,
				'date' => $today,
				'amount' => $request->payment_amount,
				'payment_concept' => $obraPlanChargeDetail->concept . ' - (' . $statusPayment . ')',
				'exchange_rate' => 0,
				'amount_usd' => 0,
				'amount_ars' => 0,
				'comments' => !empty($request->payment_description) ? $request->payment_description : null,
			];
			if ($obra->currency === 'USD') {
				$income['amount_usd'] = $request->payment_amount;
			} else if ($obra->currency === 'ARS') {
				$income['amount_ars'] = $request->payment_amount;
			}
			$income = Income::create($income);
			////////////////////////////////////////////////
			// registrar en cuenta corriente del cliente
			////////////////////////////////////////////////
			$CAService = app(CurrentAccountService::class);
			$CA_Client = [
				'project_id' => $obra->id,
				'entity_type' => 'CLIENT',
				'entity_id' => $obra->client_id,
				'currency' => $obra->currency,
			];
			// Si es el primer pago y tiene ajuste, agrega el ajuste a la CC
			if ($sumPayment === 0 && $indexTypeId) {
				$movementType = CurrentAccountMovementType::select('id')
					->where('entity_type', 'CLIENT')
					->where('name', 'Ajustes')
					->first();

				$CA_movement = [
					'date' => $today,
					'movement_type_id' => $movementType->id,
					'description' => 'Ajuste para ' . $obraPlanChargeDetail->concept,
					'amount' => ($indexAdjustmentAmount ?? 0),
					'reference_entity' => 'planChargeDetail',
					'reference_id' => $obraPlanChargeDetail->id,
					'created_by' => auth()->id()
				];
				$CAService->CAMovementAdd($CA_Client, $CA_movement);
			}

			// registrar el cobro en la CC
			// Arma arrays para crear el movimiento del cliente
			$movementType = CurrentAccountMovementType::select('id')
				->where('entity_type', 'CLIENT')
				->where('name', 'Ingreso')
				->first();

			$CA_movement = [
				'date' => $today,
				'movement_type_id' => $movementType->id,
				'description' => ' Recibo (' . $income->receipt_number . ') - ' . $income->payment_concept,
				'amount' => $request->payment_amount,
				'reference_entity' => 'ingreso',
				'reference_id' => $income->id,
				'created_by' => auth()->id()
			];
			$CAService->CAMovementAdd($CA_Client, $CA_movement);

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error("Error al registrar el pago, obraId {$obraId} DetalleId {$detailId}: " . $e->getMessage());
			throw ValidationException::withMessages(['obra' => "Error al registrar el pago, obraId {$obraId}"]);
		}
	}

	private function getAdjustmentAmount(int $obraId, int $installmentAmount, string $indexTypeCode, string $indexPeriod)
	{
		$parts = explode('_', $indexTypeCode);
		$indice = $parts[0];
		$subIndice = isset($parts[1]) ? strtolower($parts[1]) : null;

		$adjustmentCalc = 0;
		if ($indice === 'IPC') {
			$periodValue = Ipc::where('period', $indexPeriod)->first()->value;
			$adjustmentCalc = ($periodValue / 100) * $installmentAmount;
		} else if ($indice === 'CAC') {
			$obra = Obra::find($obraId);
			if (!$obra->initial_cac_index) {
				throw ValidationException::withMessages(['index' => 'La obra no tiene indice inicial.']);
			}

			$periodValue = Cac::where('period', $indexPeriod)->value($subIndice);
			$adjustmentCalc = ($periodValue / $obra->initial_cac_index - 1) * $installmentAmount;
		}
		return $adjustmentCalc;
	}
}
