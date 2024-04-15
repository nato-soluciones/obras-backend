<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Additional;
use App\Models\Contractor;

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
        $additional = Additional::with('categories.activities')->find($id);

        // Carga manualmente el proveedor (contratista) para cada actividad si el campo provider_id no es nulo
        foreach ($additional->categories as $category) {
            foreach ($category->activities as $activity) {
                if ($activity->provider_id !== null) {
                    $constractorBusinessName = Contractor::where('id', $activity->provider_id)->value('business_name');
                    $activity->provider_name = $constractorBusinessName;
                }
            }
        }

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
