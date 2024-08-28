<?php

namespace App\Http\Services\Obra;

use App\Models\Obra;
use App\Models\Obra\ObraPlanCharge;
use App\Models\Obra\ObraPlanChargeDetail;
use Illuminate\Http\Request;
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
}
