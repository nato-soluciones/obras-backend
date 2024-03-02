<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Additional;


class AdditionalController extends Controller
{

    /**
     * Get an additional by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $additional = Additional::find($id);
        return response($additional, 200);
    }


    /**
     * Edit a additional
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $additional = Additional::find($id);
        $additional->update($request->all());
        
        return response([
            'message' => 'Additional edited',
            'data' => $additional
        ], 201);
    }

    /**
     * Delete an additional by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $additional = Additional::find($id);
        $additional->delete();
        return response(['message' => 'Additional deleted'], 204);
    }
}
