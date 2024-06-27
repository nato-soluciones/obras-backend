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

    public function store(CreateFleetMovementRequest $request): Response
    {
        $image = $request->file('image');
        $movement = FleetMovement::create($request->all());

        if ($image) {
            $directory = 'public/uploads/fleet_movements/'.$movement->id;
            $imageName = 'image.' . $image->extension();
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

    public function show(int $id): Response
    {
        $movement = FleetMovement::find($id);
        return response($movement, 200);
    }
    
    public function update(UpdateFleetMovementRequest $request, int $fleetId, int $movementId): Response
    {
        $movement = FleetMovement::find($movementId);
        $movement->update($request->all());
        return response($movement, 200);
    }

    public function destroy(int $id): Response
    {
        $movement = FleetMovement::find($id);
        $movement->delete();
        return response(null, 204);
    }
}
