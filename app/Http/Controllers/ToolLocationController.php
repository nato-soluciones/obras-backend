<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\ToolLocation;
use Illuminate\Support\Facades\Log;

class ToolLocationController extends Controller
{
    /**
     * Store a new location for a tool
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, int $id): Response
    {
        $image = $request->file('image');
        $created_by = $request->user()->id;
        $data = $request->all();
        $location = ToolLocation::create(array_merge($data, ['created_by' => $created_by]));

        if ($image) {
            $directory = 'public/uploads/tools/' . $id . '/locations/' . $location->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $location->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }

        $location->save();

        return response($location, 201);
    }

    public function update(Request $request, int $id, int $locationId): Response
    {
        $location = ToolLocation::find($locationId);
        $data = $request->all();
        unset($data['image']);
        $location->update($data);

        return response($location, 200);
    }

    public function destroy(int $id, int $locationId): Response
    {
        $location = ToolLocation::find($locationId);
        if (is_null($location)) {
            return response(['message' => 'Ubicación de la Herramienta no encontrado'], 404);
        }
        $directory = 'public/uploads/tools/' . $id . '/locations/' . $location->id;

        if (Storage::deleteDirectory($directory)) {
            $location->delete();
            return response(null, 204);
        } else {
            return response(['status' => 422, 'message' => 'No se pudo borrar la ubicación de la herramienta'], 422);
        }
    }
}
