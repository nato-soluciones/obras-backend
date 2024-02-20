<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Outcome;

class OutcomeController extends Controller
{
    /** 
     * Get all outcomes list
     *
     * @return Response
     */
    public function index(): Response
    {
        $outcomes = Outcome::with('obra.client')->get();
        return response($outcomes, 200);
    }

    /**
     * Create an outcome
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $data = $request->all();
        $file = $request->file('file');

        $outcome = Outcome::create($request->all());

        if ($file) {
            $directory = 'public/uploads/outcomes/'.$outcome->id;
            $fileName = 'file.' . $file->extension();
            $filePath = Storage::putFileAs($directory, $file, $fileName, 'public');
            $outcome->file = Storage::url($filePath);

            $absolutePathToDirectory = storage_path('app/'.$directory);
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
    public function show(int $id): Response
    {
        $outcome = Outcome::with('obra.client')->find($id);
        if (is_null($outcome)) {
            return response()->json(['message' => 'Outcome not found'], 404);
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
    public function update(Request $request, int $id): Response
    {
        $outcome = Outcome::find($id);
        if (is_null($outcome)) {
            return response()->json(['message' => 'Outcome not found'], 404);
        }
        $outcome->update($request->all());
        return response($outcome, 200);
    }

    /**
     * Delete an outcome
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $outcome = Outcome::find($id);
        if (is_null($outcome)) {
            return response()->json(['message' => 'Outcome not found'], 404);
        }
        $outcome->delete();
        return response()->json(['message' => 'Outcome deleted'], 204);
    }

    public function exportList()
    {
        $outcomes = Outcome::all();
        $f = fopen('php://memory', 'r+');

        $csvTitles = [
            'Fecha',
            'Tipo',
            'Contratista',
            'Categoria',
            'Metodo de pago',
            'Fecha de pago',
            'Total',
        ];
        fputcsv($f, $csvTitles, ',');

        foreach ($outcomes as $item) {
            $csvRow = [
                $item->date,
                $item->type,
                $item->contractor->business_name,
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
