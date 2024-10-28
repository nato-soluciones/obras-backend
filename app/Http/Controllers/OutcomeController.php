<?php

namespace App\Http\Controllers;

use App\Enums\Outcome as EnumsOutcome;
use App\Models\CurrentAccountMovementType;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Outcome;
use App\Services\CurrentAccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OutcomeController extends Controller
{
    /** 
     * Get all outcomes list
     *
     * @return Response
     */
    public function index(Request $request, int $obraId): Response
    {
        $outcomes = Outcome::where('obra_id', $obraId)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return response($outcomes, 200);
    }

    public function listAll(Request $request, int $obraId): Response
    {
        $incomes = Outcome::where('obra_id', $obraId)
            ->withTrashed()
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return response($incomes, 200);
    }
    /**
     * Create an outcome
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
        $currency = $obra->currency;

        $request->merge(['obra_id' => $obraId]);

        $file = $request->file('file');
        try {
            $outcome = Outcome::create($request->all());

            if ($file) {
                $directory = 'public/uploads/obras/' . $obraId . '/outcomes/' . $outcome->id;
                $fileName = 'file.' . $file->extension();
                $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
                $outcome->file = Storage::url($filePath);

                $absolutePathToDirectory = storage_path('app/' . $directory);
                chmod($absolutePathToDirectory, 0755);
            }

            $outcome->save();

            // Arma arrays para crear el movimiento del proveedor
            $movementType = CurrentAccountMovementType::select('id')
                ->where('entity_type', 'PROVIDER')
                ->where('name', 'Egreso')
                ->first();

            $CAData = [
                'project_id' => $obra->id,
                'entity_type' => 'PROVIDER',
                'entity_id' => $outcome->contractor_id,
                'currency' => $currency,
            ];

            $CAMovementData = [
                'date' => $outcome->payment_date,
                'movement_type_id' => $movementType->id,
                'description' => '(' . $outcome->id . ') Pago  - ' . EnumsOutcome::$types[$outcome->type],
                'amount' => $outcome->total,
                'reference_entity' => 'egreso',
                'reference_id' => $outcome->id,
                'created_by' => auth()->user()->id
            ];

            $CAService = app(CurrentAccountService::class);
            $CAService->CAMovementAdd($CAData, $CAMovementData);


            return response($outcome, 201);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response(['status' => 500, 'message' => 'Error al guardar el egreso'], 500);
        }
    }

    /**
     * Get an outcome
     *
     * @param int $id
     * @return Response
     */
    public function show(int $obraId, int $outcomeId): Response
    {
        $outcome = Outcome::with('obra.client')->find($outcomeId);
        if (is_null($outcome)) {
            return response()->json(['message' => 'Egreso no encontrado'], 404);
        }
        return response($outcome, 200);
    }

    /**
     * Update an outcome
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $obraId, int $outcomeId): Response
    {
        // Verifica que la obra exista
        $obra = Obra::find($obraId);
        if (is_null($obra)) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        $outcome = Outcome::find($outcomeId);
        if (is_null($outcome)) {
            return response()->json(['message' => 'Egreso no encontrado'], 404);
        }
        $currency = $obra->currency;

        try {
            $outcome->fill($request->all());

            $file = $request->file('file');
            if ($file) {
                $directory = 'public/uploads/obras/' . $obraId . '/outcomes/' . $outcome->id;
                $fileName = 'file.' . $file->extension();
                $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
                $outcome->file = Storage::url($filePath);

                $absolutePathToDirectory = storage_path('app/' . $directory);
                chmod($absolutePathToDirectory, 0755);
            }

            $outcome->save();

            // Arma arrays para actualizar el movimiento del proveedor
            $CAData = [
                'project_id' => $obra->id,
                'entity_type' => 'PROVIDER',
                'entity_id' => $outcome->contractor_id,
                'currency' => $currency,
            ];

            $CA_movement = [
                'date' => Date('Y-m-d'),
                'amount' => $outcome->total,
                'reference_entity' => 'egreso',
                'reference_id' => $outcome->id,
                'created_by' => auth()->user()->id
            ];

            $CAService = app(CurrentAccountService::class);
            $CAService->CAMovementUpdateByReference($CAData, $CA_movement);
            return response($outcome, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response(['status' => 500, 'message' => 'Error al actualizar el egreso'], 500);
        }
    }

    /**
     * Delete an outcome
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $obraId, int $outcomeId)
    {
        // Verifica que la obra exista
        $obra = Obra::find($obraId);
        if (is_null($obra)) {
            return response()->json(['message' => 'Obra no encontrada'], 404);
        }

        $currency = $obra->currency;
        try {
            $outcome = Outcome::findOrFail($outcomeId);
            $outcome->delete();

            // Arma arrays para eliminar el movimiento del cliente
            $movementType = CurrentAccountMovementType::select('id')
                ->where('entity_type', 'PROVIDER')
                ->where('name', 'Egreso - Eliminado')
                ->first();

            $CAData = [
                'project_id' => $obra->id,
                'entity_type' => 'PROVIDER',
                'entity_id' => $outcome->contractor_id,
                'currency' => $currency,
            ];

            $CA_movement = [
                'date' => Date('Y-m-d'),
                'movement_type_id' => $movementType->id,
                'reference_entity' => 'egreso',
                'reference_id' => $outcome->id,
                'created_by' => auth()->user()->id
            ];

            $CAService = app(CurrentAccountService::class);
            $CAService->CAMovementDeleteByReference($CAData, $CA_movement);

            return response(['message' => 'Egreso eliminado correctamente'], 204);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Egreso no encontrado'], 404);
        }
    }

    public function exportList(int $obraId)
    {
        $outcomes = Outcome::where('obra_id', $obraId)
            ->orderBy('date', 'desc')
            ->get();
        $f = fopen('php://memory', 'r+');

        $csvTitles = [
            'Fecha',
            'Comprobante',
            'Tipo de Movimiento',
            'Detalle',
            'Método de pago',
            'Fecha de pago',
            'Importe Total',
        ];
        fputcsv($f, $csvTitles, ',');

        foreach ($outcomes as $item) {
            $comprobante = $item->document_type ? EnumsOutcome::$documentTypes[$item->document_type] : '';
            $comprobante .= $item->order ? ' ' . $item->order : '';
            $csvRow = [
                $item->date,
                $comprobante,
                EnumsOutcome::$types[$item->type],
                $item->description,
                EnumsOutcome::$paymentMethods[$item->payment_method],
                $item->payment_date,
                $item->total,
            ];
            fputcsv($f, $csvRow, ',');
        }

        rewind($f);
        $csv = stream_get_contents($f);
        fclose($f);

        return response(['datos' =>  $csv], 200);
    }
}
