<?php

namespace App\Http\Controllers;

use App\Models\StoreMovementReason;
use Illuminate\Http\Response;

class StoreMovementReasonController extends Controller
{
    public function index(): Response
    {
        $reasons = StoreMovementReason::all();
        return response($reasons, 200);
    }

    public function show(string $id): Response
    {
        $reason = StoreMovementReason::find($id);
        
        if (!$reason) {
            return response([
                'message' => 'Motivo no encontrado'
            ], 404);
        }

        return response($reason, 200);
    }
} 