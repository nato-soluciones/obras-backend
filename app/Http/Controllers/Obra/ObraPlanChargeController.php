<?php

namespace App\Http\Controllers\Obra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Obra\PlanCharge\StorePlanChargeRequest;
use App\Http\Services\Obra\ObraPlanChargeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraPlanChargeController extends Controller
{
    private $obraPlanChargeService;

    public function __construct(ObraPlanChargeService $obraPlanChargeService)
    {
        $this->obraPlanChargeService = $obraPlanChargeService;
    }


    public function store(StorePlanChargeRequest $request, int $obraId)
    {
        $planCharge = $request->planChargeData;
        $installments = $request->installments;
        try {
            $planChargeResult = $this->obraPlanChargeService->store($planCharge, $installments, $obraId);
            return response()->json($planChargeResult, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear el plan de cobros'], 500);
        }

    }
}
