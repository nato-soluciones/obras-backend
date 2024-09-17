<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\Fleet;

use App\Http\Requests\Fleet\CreateFleetRequest;
use App\Http\Requests\Fleet\UpdateFleetRequest;

class FleetController extends Controller
{
    public function index(): Response
    {
        $fleets = Fleet::with('last_movement', 'next_movement')
            ->orderBy('purchase_date', 'desc')
            ->get();
        return response($fleets, 200);
    }

    public function store(CreateFleetRequest $request): Response
    {
        $data = $request->all();
        $image = $request->file('image');
        $fleet = Fleet::create($request->all());

        if ($image) {
            $directory = 'public/uploads/fleets/' . $fleet->id;
            $imageName = 'image.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $fleet->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $fleet->save();

        return response($fleet, 201);
    }

    public function show(int $id): Response
    {
        $fleet = Fleet::with(['movements' => function ($q) {
            $q->orderBy('date', 'desc')->orderBy('id', 'desc');
        }, 'documents', 'last_movement', 'next_movement'])->find($id);
        return response($fleet, 200);
    }

    public function update(UpdateFleetRequest $request, int $id): Response
    {
        $fleet = Fleet::find($id);

        $new_image = $request->file('new_image');
        $fleet->update($request->all());

        if ($new_image) {
            $directory = 'public/uploads/fleets/' . $fleet->id;
            $imageName = 'image.' . $new_image->extension();
            $imagePath = Storage::putFileAs($directory, $new_image, $imageName, 'public');
            $fleet->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/' . $directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $fleet->save();

        return response($fleet, 200);
    }

    public function destroy(int $id): Response
    {
        $fleet = Fleet::find($id);
        $fleet->delete();
        return response(null, 204);
    }
}
