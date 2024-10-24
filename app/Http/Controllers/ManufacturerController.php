<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\Manufacturer;

class ManufacturerController extends Controller
{
    public function index(): Response
    {
        $manufacturers = Manufacturer::with('category')
            ->orderBy('name', 'asc')
            ->get();
        return response($manufacturers, 200);
    }

    public function store(Request $request): Response
    {
        $data = $request->all();
        $image = $request->file('image');
        $manufacturer = Manufacturer::create($request->all());

        if ($image) {
            $directory = 'public/uploads/manufacturers/' . $manufacturer->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $manufacturer->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $manufacturer->save();

        return response($manufacturer, 201);
    }

    public function show(int $id): Response
    {
        $manufacturer = Manufacturer::with('category', 'files')->find($id);
        return response($manufacturer, 200);
    }

    public function update(Request $request, int $id): Response
    {
        $manufacturer = Manufacturer::find($id);
        $manufacturer->fill($request->all());
        $image = $request->file('image');

        if ($image) {
            $directory = 'public/uploads/manufacturers/' . $manufacturer->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $manufacturer->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }

        $manufacturer->save();

        return response($manufacturer, 200);
    }

    public function destroy(int $id): Response
    {
        $manufacturer = Manufacturer::find($id);
        if (is_null($manufacturer)) {
            return response(['status' => 404, 'message' => 'Producto no encontrado'], 404);
        }
        $directory = 'public/uploads/manufacturers/' . $manufacturer->id;

        if (Storage::deleteDirectory($directory)) {
            $manufacturer->delete();
            return response(null, 204);
        } else {
            return response(['status' => 422, 'message' => 'No se pudo borrar el producto'], 422);
        }
    }
}
