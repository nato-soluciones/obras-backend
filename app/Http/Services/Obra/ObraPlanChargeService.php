<?php

namespace App\Http\Services\Obra;

use App\Models\Obra\ObraPlanCharge;
use App\Models\Obra\ObraPlanChargeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraPlanChargeService
{

  public function store(array $planChargeData, array $installments, int $obraId)
  {
    DB::beginTransaction();
    try {
      // Crea el registro de ObraPlanCharge
      $obraPlanCharge = ObraPlanCharge::create([
        'obra_id' => $obraId,
        'financed_amount' => $planChargeData['financed_amount'],
        'installment_count' => $planChargeData['installment_count'],
        'installment_frequency' => $planChargeData['installment_frequency'],
        'installment_first_due_date' => $planChargeData['installment_first_due_date'],
        'created_by_id' => auth()->id(),
      ]);

      // Recorre cada cuota e incluir el obra_plan_charge_id
      foreach ($installments as $installment) {
        $installment['obra_plan_charge_id'] = $obraPlanCharge->id;
        $installment['type'] = 'INSTALLMENT';
        $installment['status'] = 'PENDING';
        $installment['installment_number'] = $installment['number'];
        $installment['total_amount'] = $installment['installment_amount'];
        unset($installment['number']);

        ObraPlanChargeDetail::create($installment);
      }

      DB::commit();

      return $obraPlanCharge;
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error("Error al crear el plan de cobro, obraId {$obraId}:" . $e->getMessage());
      throw ValidationException::withMessages(['obra' => "Error al crear el plan de cobro, obraId {$obraId}"]);
    }
  }
}
