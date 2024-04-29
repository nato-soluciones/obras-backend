<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Ipc;

class IpcController extends Controller
{
    /**
     * List all ipcs
     *
     * @return Response
     */
    public function index(): Response
    {
        $ipcs = Ipc::orderBy('period', 'desc')->get();
        return response($ipcs);
    }

    /**
     * Creates an ipc
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $ipc = Ipc::create($request->all());        
        return response($ipc, 201);
    }

    /**
     * Update an ipc by id
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $ipc = Ipc::find($id);
        $ipc->update($request->all());

        return response($ipc, 200);
    }

    /**
     * Delete an ipc by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $ipc = Ipc::find($id);
        $ipc->delete();

        return response(['message' => 'IPC deleted'], 204);;
    }
}
