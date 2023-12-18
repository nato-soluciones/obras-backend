<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Contractor;

class ContractorController extends Controller
{
    /**
     * Get all contractors
     *
     * @return Response
     */
    public function index(): Response
    {
        $contractors = Contractor::all();
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
        $contractor = Contractor::find($id);
        return response($contractor, 200);
    }

    /**
     * Create an contractor
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
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
    public function update(Request $request, int $id): Response
    {
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
}
