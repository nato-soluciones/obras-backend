<?php

namespace App\Http\Controllers;

use App\Models\StoreMovementStatus;
use Illuminate\Http\Request;

class StoreMovementStatusController extends Controller
{
    public function index()
    {
        $movementStatuses = StoreMovementStatus::all();

        return response($movementStatuses, 200);
    }

    public function show($id)
    {
        $storeMovementStatus = StoreMovementStatus::find($id);
        if (!$storeMovementStatus) {
            return response()->json(['error' => 'No se encontró el estado de movimiento'], 404);
        }

        return response()->json($storeMovementStatus, 200);
    }
}
