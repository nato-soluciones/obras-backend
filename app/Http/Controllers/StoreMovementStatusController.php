<?php

namespace App\Http\Controllers;

use App\Models\StoreMovementStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreMovementStatusController extends Controller
{
    public function index()
    {
        $movementStatuses = StoreMovementStatus::get();

        return response($movementStatuses, 200);
    }

    public function show(string $id): Response
    {
        $status = StoreMovementStatus::find($id);

        if (!$status) {
            return response([
                'error' => 'Estado de movimiento no encontrado'
            ], 404);
        }

        return response($status, 200);
    }
}
