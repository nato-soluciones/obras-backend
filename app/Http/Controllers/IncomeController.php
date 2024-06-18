<?php

namespace App\Http\Controllers;

use App\Models\CurrentAccountMovementType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

use App\Models\Income;
use App\Models\Obra;
use App\Notifications\IncomeCreated;
use App\Services\CurrentAccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class IncomeController extends Controller
{

    /** 
     * Get all income list
     *
     * @return Response
     */
    public function index(Request $request, int $obraId): Response
    {
        $incomes = Income::where('obra_id', $obraId)->orderBy('receipt_number', 'desc')->get();
        return response($incomes, 200);
    }

    public function listAll(Request $request, int $obraId): Response
    {
        $incomes = Income::where('obra_id', $obraId)
            ->withTrashed()
            ->orderBy('receipt_number', 'desc')
            ->get();
        return response($incomes, 200);
    }
    /**
     * Create an income
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, int $obraId): Response
    {
        // Verifica que la obra exista
        $obra = Obra::find($obraId);
        if (is_null($obra)) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }
        $clientId = $obra->budget->client_id;
        $currency = $obra->budget->currency;
        $amount = ($obra->budget->currency === 'ARS') ? $request->amount_ars : $request->amount_usd;

        $request->merge(['obra_id' => $obraId]);

        // Crea el ingreso
        try {
            $income = Income::create($request->all());

            // Arma arrays para crear el movimiento del cliente
            $movementType = CurrentAccountMovementType::select('id')
                ->where('entity_type', 'CLIENT')
                ->where('name', 'Ingreso')
                ->first();

            $CA_Client = [
                'project_id' => $obra->id,
                'entity_type' => 'CLIENT',
                'entity_id' => $clientId,
                'currency' => $currency,
            ];
            $CA_movement = [
                'date' => Date('Y-m-d'),
                'movement_type_id' => $movementType->id,
                'description' => ' Cobro (' . $income->receipt_number . ') - ' . $income->payment_concept,
                'amount' => $amount,
                'reference_entity' => 'ingreso',
                'reference_id' => $income->id,
                'created_by' => auth()->user()->id
            ];

            $CAService = app(CurrentAccountService::class);
            $CAService->CAMovementAdd($CA_Client, $CA_movement);

            return response($income, 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response(['status' => 500, 'message' => 'Error al guardar el ingreso'], 500);
        }
    }

    public function show(int $obraId, int $incomeId): Response
    {
        $income = Income::find($incomeId);
        if (is_null($income)) {
            return response(['message' => 'Ingreso no encontrado'], 404);
        }
        return response($income, 200);
    }

    public function update(Request $request, int $obraId, int $incomeId): Response
    {
        // Verifica que la obra exista
        $obra = Obra::find($obraId);
        if (is_null($obra)) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        $income = Income::find($incomeId);
        if (is_null($income)) {
            return response()->json(['message' => 'Ingreso no encontrado'], 404);
        }

        $clientId = $obra->budget->client_id;
        $currency = $obra->budget->currency;
        $amount = ($obra->budget->currency === 'ARS') ? $request->amount_ars : $request->amount_usd;

        try {
            $income->update($request->all());

            // Arma arrays para actualizar el movimiento del cliente
            $CAData = [
                'project_id' => $obra->id,
                'entity_type' => 'CLIENT',
                'entity_id' => $clientId,
                'currency' => $currency,
            ];
            $CA_movement = [
                'date' => Date('Y-m-d'),
                'amount' => $amount,
                'reference_entity' => 'ingreso',
                'reference_id' => $income->id,
                'created_by' => auth()->user()->id
            ];

            $CAService = app(CurrentAccountService::class);
            $CAService->CAMovementUpdateByReference($CAData, $CA_movement);
            return response($income, 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response(['status' => 500, 'message' => 'Error al actualizar el ingreso'], 500);
        }
    }

    public function destroy(int $obraId, int $incomeId): Response
    {
        // Verifica que la obra exista
        $obra = Obra::find($obraId);
        if (is_null($obra)) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        $clientId = $obra->budget->client_id;
        $currency = $obra->budget->currency;
        try {
            $income = Income::findOrFail($incomeId);

            $income->delete();

            // Arma arrays para eliminar el movimiento del cliente
            $movementType = CurrentAccountMovementType::select('id')
                ->where('entity_type', 'CLIENT')
                ->where('name', 'Ingreso - Eliminado')
                ->first();
            $CAData = [
                'project_id' => $obra->id,
                'entity_type' => 'CLIENT',
                'entity_id' => $clientId,
                'currency' => $currency,
            ];
            $CA_movement = [
                'date' => Date('Y-m-d'),
                'movement_type_id' => $movementType->id,
                'reference_entity' => 'ingreso',
                'reference_id' => $income->id,
                'created_by' => auth()->user()->id
            ];

            $CAService = app(CurrentAccountService::class);
            $CAService->CAMovementDeleteByReference($CAData, $CA_movement);

            return response(['message' => 'Ingreso eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response(['status' => 500, 'message' => 'Error al eliminar el ingreso'], 500);
        }
    }

    public function exportList(int $obraId)
    {
        $incomes = Income::where('obra_id', $obraId)
            ->withTrashed()
            ->orderBy('receipt_number', 'desc')
            ->get();
        $f = fopen('php://memory', 'r+');

        $csvTitles = [
            'Fecha',
            'Recibo',
            'Obra',
            'Concepto',
            'Importe USD',
            'Importe ARS',
            'Tipo de Cambio',
        ];
        fputcsv($f, $csvTitles, ',');

        foreach ($incomes as $item) {
            $csvRow = [
                $item->date,
                $item->receipt_number,
                $item->obra->name,
                $item->payment_concept,
                $item->amount_usd,
                $item->amount_ars,
                $item->exchange_rate,
            ];
            fputcsv($f, $csvRow, ',');
        }

        rewind($f);
        $csv = stream_get_contents($f);
        fclose($f);

        return response(['datos' =>  $csv], 200);
    }
}
