<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyCostRequest;
use App\Http\Services\Company\CompanyCostService;
use App\Models\Company\CompanyCost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyCostController extends Controller
{
    public function index(Request $request, CompanyCostService $companyCostService)
    {
        try {
            $companyCosts = $companyCostService->list($request);
            return response($companyCosts, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al obtener los costos'], 500);
        }
    }

    public function store(StoreCompanyCostRequest $request)
    {
        try {
            // Extrae el año y mes de la fecha
            $period = Carbon::parse($request->registration_date)->format('Y-m');
            $request->merge([
                'period' => $period,
                'created_by_id' => auth()->id()
            ]);
            CompanyCost::create($request->all());
            return response()->json(['message' => 'Costo guardado correctamente'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error guardar el costo'], 500);
        }
    }

    public function show(int $companyCostId)
    {
        try {
            $companyCost = CompanyCost::findOrFail($companyCostId);
            return response($companyCost, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error guardar el costo'], 500);
        }
    }

    public function update(Request $request, int $companyCostId)
    {
        try {
            $companyCost = CompanyCost::findOrFail($companyCostId);
            $period = Carbon::parse($request->registration_date)->format('Y-m');
            $request->merge(['period' => $period]);
            $companyCost->update($request->all());
            return response($companyCost, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al actualizar el costo'], 500);
        }
    }

    public function destroy(int $companyCostId)
    {
        try {
            $companyCost = CompanyCost::findOrFail($companyCostId);
            $companyCost->delete();
            return response()->json(['message' => 'Costo eliminado correctamente'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al eliminar el costo'], 500);
        }
    }

    public function paymentRegistration(Request $request, int $companyCostId)
    {
        try {
            // validar que venga la fecha de pago

            $dataSave = [
                'id' => $companyCostId,
                'payment_status' => 'PAID',
                'payment_date' => $request->payment_date
            ];


            $companyCost = CompanyCost::findOrFail($companyCostId);
            $companyCost->update($dataSave);
            return response($companyCost, 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Error al registrar el pago'], 500);
        }
    }

    public function getChartData()
    {
        $companyCosts = CompanyCost::select(DB::raw('SUM(amount) as total_amount'), 'category_id', 'period')
            ->with('category')
            ->groupBy('category_id', 'period')
            ->orderBy('period', 'desc')
            ->whereIn('period', function ($query) {
                $query->select('period')
                    ->from('company_costs')
                    ->groupBy('period')
                    ->orderBy('period', 'desc')
                    ->limit(3);
            })
            ->get();

        $data = $companyCosts->map(function ($cost) {
            return [
                'total_amount' => $cost->total_amount,
                'category_name' => $cost->category->name,
                'period' => $cost->period,
            ];
        });

        return response()->json($data);
    }

    public function getPeriods()
    {
        $companyCosts = CompanyCost::distinct('period')
            ->select('period')
            ->orderBy('period', 'desc')
            ->get();
        
        $companyCosts = $companyCosts->map(function ($cost) {
            return $cost->period;
        });

        return response()->json($companyCosts);
    }
}
