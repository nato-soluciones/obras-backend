<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Contractor;

use App\Http\Requests\Contractor\CreateContractorRequest;
use App\Http\Requests\Contractor\UpdateContractorRequest;
use Illuminate\Support\Facades\Log;

class ContractorController extends Controller
{
    /**
     * Get all contractors
     *
     * @return Response
     */
    public function index(): Response
    {
        $contractors = Contractor::with('industries')->orderBy('business_name')->get();
        return response($contractors, 200);
    }

    /**
     * Get an contractor by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $contractor = Contractor::from('contractors as c')
            ->join('contractor_industries as i', 'c.industry', '=', 'i.code')
            ->leftJoin('banks as b', 'c.bank', '=', 'b.code')
            ->where('c.id', $id)
            ->select('c.*', 'i.name as industry_name', 'b.name as bank_name')
            ->first();
        return response($contractor, 200);
    }

    /**
     * Create an contractor
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateContractorRequest $request): Response
    {
        // Check cuit is unique
        if (Contractor::where('cuit', $request->cuit)->exists()) {
            return response(['message' => 'El CUIT ya está en uso'], 409);
        }

        $contractor = Contractor::create($request->all());
        return response([
            'message' => 'Contractor created',
            'data' => $contractor
        ], 201);
    }

    /**
     * Update an contractor by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateContractorRequest $request, int $id): Response
    {
        // Ckeck cuit is unique
        if (Contractor::where('cuit', $request->cuit)->where('id', '!=', $id)->exists()) {
            return response(['message' => 'El CUIT ya está en uso'], 409);
        }

        $contractor = Contractor::find($id);
        $contractor->update($request->all());

        return response([
            'message' => 'Contractor updated',
            'data' => $contractor
        ], 200);
    }

    /**
     * Delete an contractor by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $contractor = Contractor::find($id);
        $contractor->delete();

        return response(['message' => 'Contractor deleted'], 204);
    }

    /**
     * Export all users in a CSV file format
     *
     * @return Response
     */
    public function exportList()
    {
        $contractors = Contractor::all();
        $f = fopen('php://memory', 'r+');

        $csvTitles = [
            'Razón Social',
            'Nombre de Fantasía',
            'Condición de IVA',
            'CUIT',
            'Contacto de Referencia',
            'Email',
            'Teléfono',
            'Ciudad',
            'Dirección',
            'CP',
        ];
        fputcsv($f, $csvTitles, ',');

        foreach ($contractors as $item) {
            $csvRow = [
                $item->business_name,
                $item->trade_name,
                $item->condition,
                $item->cuit,
                $item->referral,
                $item->email,
                $item->phone,
                $item->city,
                $item->address,
                $item->zip,
            ];
            fputcsv($f, $csvRow, ',');
        }

        rewind($f);
        $csv = stream_get_contents($f);
        fclose($f);

        return response(['datos' =>  $csv], 200);
    }
}
