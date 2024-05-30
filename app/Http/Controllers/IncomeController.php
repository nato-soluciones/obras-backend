<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

use App\Models\Income;
use App\Notifications\IncomeCreated;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $incomes = Income::where('obra_id', $obraId)->withTrashed()->orderBy('receipt_number', 'desc')->get();
        return response($incomes, 200);
    }
    /**
     * Create an income
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $income = Income::create($request->all());
        return response($income, 201);
    }

    /**
     * Get an income
     *
     * @param int $id
     * @return Response
     */
    public function show(int $obraId, int $incomeId): Response
    {
        $income = Income::find($incomeId);
        if (is_null($income)) {
            return response(['message' => 'Ingreso no encontrado'], 404);
        }
        return response($income, 200);
    }

    /**
     * Update an income
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $obraId, int $incomeId): Response
    {
        $income = Income::find($incomeId);
        if (is_null($income)) {
            return response()->json(['message' => 'Income not found'], 404);
        }
        $income->update($request->all());
        return response($income, 200);
    }

    /**
     * Delete an income
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $obraId, int $incomeId): Response
    {
        try {
            $income = Income::findOrFail($incomeId);
            $income->delete();

            return response(['message' => 'Ingreso eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Ingreso no encontrado'], 404);
        }
    }

    public function exportList(int $obraId)
    {
        $incomes = Income::where('obra_id', $obraId)->withTrashed()->orderBy('receipt_number', 'desc')->get();
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
