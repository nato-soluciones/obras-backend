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
    public function index(Request $request): Response
    {
        $perPage = 20;
        $status = $request->input('status', 'ALL');

        $fleets = Fleet::with('last_movement', 'next_movement')
            ->when($status !== 'ALL', function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('purchase_date', 'desc')
            ->paginate($perPage);

        $response = [
            'data' => $fleets->items(),
            'current_page' => $fleets->currentPage(),
            'last_page' => $fleets->lastPage(),
            'total' => $fleets->total(),
        ];
        return response($response, 200);
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
        if (is_null($fleet)) {
            return response(['status' => 404, 'message' => 'Vehículo no encontrado'], 404);
        }
        $directory = 'public/uploads/fleets/' . $fleet->id;

        if (Storage::deleteDirectory($directory)) {
            $fleet->delete();
            return response(null, 204);
        } else {
            return response(['status' => 422, 'message' => 'No se pudo borrar el vehículo'], 422);
        }
    }
}
