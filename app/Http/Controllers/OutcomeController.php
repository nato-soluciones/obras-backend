<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Outcome;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function store(Request $request): Response
    {
        $file = $request->file('file');

        $outcome = Outcome::create($request->all());

        if ($file) {
            $directory = 'public/uploads/outcomes/' . $outcome->id;
            $fileName = 'file.' . $file->extension();
            $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
            $outcome->file = Storage::url($filePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }

        $outcome->save();

        return response($outcome, 201);
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
        $outcome = Outcome::find($outcomeId);
        if (is_null($outcome)) {
            return response()->json(['message' => 'Egreso no encontrado'], 404);
        }
        $outcome->fill($request->all());

        $file = $request->file('file');
        if ($file) {
            $directory = 'public/uploads/outcomes/' . $outcome->id;
            $fileName = 'file.' . $file->extension();
            $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
            $outcome->file = Storage::url($filePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }


        $outcome->save();
        return response($outcome, 200);
    }

    /**
     * Delete an outcome
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $obraId, int $outcomeId)
    {
        try {
            $outcome = Outcome::findOrFail($outcomeId);
            $outcome->delete();
            return response(['message' => 'Egreso eliminado correctamente'], 204);
        } catch (ModelNotFoundException $e) {
            return response(['error' => 'Egreso no encontrado'], 404);
        }
    }

    public function exportList(int $obraId)
    {
        $outcomes = Outcome::where('obra_id', $obraId)
            ->withTrashed()
            ->orderBy('date', 'desc')
            ->get();
        $f = fopen('php://memory', 'r+');

        $csvTitles = [
            'Fecha',
            'Tipo',
            'Contratista',
            'Categoría',
            'Método de pago',
            'Fecha de pago',
            'Total',
        ];
        fputcsv($f, $csvTitles, ',');

        foreach ($outcomes as $item) {
            $csvRow = [
                $item->date,
                $item->type,
                optional($item->contractor)->business_name,
                '',
                $item->payment_method,
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
