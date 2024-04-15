<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\Manufacturer;

class ManufacturerController extends Controller
{
    /**
     * Get all manufacturers
     *
     * @return Response
     */
    public function index(): Response
    {
        $manufacturers = Manufacturer::with('category')->get();
        return response($manufacturers, 200);
    }

    /**
     * Create a manufacturer
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $data = $request->all();
        $image = $request->file('image');
        $manufacturer = Manufacturer::create($request->all());

        if ($image) {
            $directory = 'public/uploads/manufacturers/'.$manufacturer->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $manufacturer->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/'.$directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $manufacturer->save();

        return response($manufacturer, 201);
    }

    /**
     * Get a manufacturer by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $manufacturer = Manufacturer::with('category', 'files')->find($id);
        return response($manufacturer, 200);
    }

    /**
     * Update a manufacturer by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $manufacturer = Manufacturer::find($id);
        $manufacturer->fill($request->all());
        $image = $request->file('image');

        if ($image) {
            $directory = 'public/uploads/manufacturers/'.$manufacturer->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $manufacturer->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/'.$directory);
            chmod($absolutePathToDirectory, 0755);
        }

        $manufacturer->save();

        return response($manufacturer, 200);
    }

    /**
     * Delete a manufacturer by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $manufacturer = Manufacturer::find($id);
        $manufacturer->delete();

        return response(['message' => 'Manufacturer deleted'], 204);
    }
}

