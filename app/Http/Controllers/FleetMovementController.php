<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\FleetMovement;

use App\Http\Requests\FleetMovement\CreateFleetMovementRequest;
use App\Http\Requests\FleetMovement\UpdateFleetMovementRequest;

class FleetMovementController extends Controller
{
    public function index(): Response
    {
        $movements = FleetMovement::all();
        return response($movements, 200);
    }

    public function store(CreateFleetMovementRequest $request, int $fleetId): Response
    {
        $image = $request->file('image');
        $movement = FleetMovement::create($request->all());

        if ($image) {
            $directory = 'public/uploads/fleets/' . $fleetId . '/movements/'.$movement->id;
            $imageName = 'file.' . $image->extension();
            $imagePath = Storage::putFileAs($directory, $image, $imageName, 'public');
            $movement->image = Storage::url($imagePath);

            $absolutePathToDirectory = storage_path('app/'.$directory);
            chmod($absolutePathToDirectory, 0755);
        }
        $movement->save();

        $fleet = $movement->fleet;
        $fleet->mileage = $movement->mileage;
        $fleet->save();

        return response($movement, 201);
    }

    public function show(int $fleetId, int $movementId): Response
    {
        $movement = FleetMovement::find($movementId);
        return response($movement, 200);
    }
    
    public function update(UpdateFleetMovementRequest $request, int $fleetId, int $movementId): Response
    {
        $movement = FleetMovement::find($movementId);
        $movement->update($request->all());
        return response($movement, 200);
    }

    public function destroy(int $fleetId, int $movementId): Response
    {
        $movement = FleetMovement::find($movementId);
        if (is_null($movement)) {
            return response(['status' => 404, 'message' => 'Movimiento no encontrado'], 404);
        }

        $directory = 'public/uploads/fleets/' . $fleetId . '/movements/' . $movementId;
        if (Storage::deleteDirectory($directory)) {
            $movement->delete();
            return response(null, 204);
        } else {
            return response(['status' => 422, 'message' => 'No se pudo borrar el movimiento'], 422);
        }
    }
}
