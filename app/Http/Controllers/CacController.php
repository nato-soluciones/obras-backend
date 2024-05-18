<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Cac;

use App\Http\Requests\Cac\CreateCacRequest;
use App\Http\Requests\Cac\UpdateCacRequest;

class CacController extends Controller
{
    /**
     * List all cacs
     *
     * @return Response
     */
    public function index(): Response
    {
        $cacs = Cac::orderBy('period', 'desc')->get();
        return response($cacs);
    }

    /**
     * Creates an cac
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateCacRequest $request): Response
    {
        $cac = Cac::create($request->all());        
        return response($cac, 201);
    }

    /**
     * Update an cac by id
     *
     * @param Request $request
     * @return Response
     */
    public function update(UpdateCacRequest $request, int $id): Response
    {
        $cac = Cac::find($id);
        $cac->update($request->all());

        return response($cac, 200);
    }

    /**
     * Delete an cac by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $cac = Cac::find($id);
        $cac->delete();

        return response(['message' => 'CAC deleted'], 204);;
    }
}
