<?php

namespace App\Http\Controllers\Obra;

use App\Http\Controllers\Controller;
use App\Http\Resources\ObraPlanChargeDetailResource;
use App\Http\Services\Obra\ObraPlanChargeDetailService;
use App\Models\Obra\ObraPlanChargeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ObraPlanChargeDetailController extends Controller
{

    private $obraPlanChargeDetailService;
    public function __construct(ObraPlanChargeDetailService $obraPlanChargeDetailService)
    {
        $this->obraPlanChargeDetailService = $obraPlanChargeDetailService;
    }


    public function index(Request $request, int $obraId)
    {
        $per_page = 20;
        $payment_status = $request->input('paymentStatus');

        $details = ObraPlanChargeDetail::whereHas('planCharge', function ($query) use ($obraId) {
            $query->where('obra_id', $obraId);
        })->with(['payments' => function ($q) {
            $q->select('obra_plan_charge_detail_id', 'id', 'date', 'amount', 'description')->orderBy('date', 'asc');
        }, 'indexType' => function ($q) {
            $q->select('id', 'name');
        }])->when($payment_status, function ($query, $payment_status) {
            $query->where('status', $payment_status);
        })
            ->orderBy('due_date', 'asc')
            ->orderBy('id', 'asc')
            ->paginate($per_page);

        // Log::info($details);
        return response()->json([
            'data' => ObraPlanChargeDetailResource::collection($details)->response()->getData(true)['data'],
            'current_page' => $details->currentPage(),
            'last_page' => $details->lastPage(),
        ]);
    }

    public function indexTotals(int $obraId)
    {
        // Calcular el total financiado de total_amount
        $total_amount = ObraPlanChargeDetail::whereHas('planCharge', function ($query) use ($obraId) {
            $query->where('obra_id', $obraId);
        })->sum('total_amount');

        // Calcular el total financiado de installment_amount
        $installment_amount = ObraPlanChargeDetail::whereHas('planCharge', function ($query) use ($obraId) {
            $query->where('obra_id', $obraId);
        })->sum('installment_amount');

        return response()->json([
            'total_amount' => $total_amount,
            'installment_amount' => $installment_amount,
        ]);
    }

    public function show(int $obraId, int $detailId)
    {
        $detail = ObraPlanChargeDetail::whereHas('planCharge', function ($query) use ($obraId) {
            $query->where('obra_id', $obraId);
        })->where('id', $detailId)->get();

        return response($detail, 200);
    }

    public function store(Request $request, int $obraId)
    {
        try {
            $obraPlanChargeDetail = $this->obraPlanChargeDetailService->store($request, $obraId);
            return response()->json($obraPlanChargeDetail, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al crear ajuste en el plan de cobros'], 500);
        }
    }


    public function charge(Request $request, int $obraId, int $detailId)
    {
        try {
            $this->obraPlanChargeDetailService->charge($request, $obraId, $detailId);
            return response()->json(['message' => 'Cobro realizado correctamente'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al realizar el cobro'], 500);
        }
    }
}
