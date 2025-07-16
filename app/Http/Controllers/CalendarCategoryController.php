<?php

namespace App\Http\Controllers;

use App\Http\Resources\CalendarEventCategoryResource;
use App\Models\CalendarEventCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get all available calendar categories
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $categories = CalendarEventCategory::where(function($query) use ($user) {
            $query->where('is_system', true)
                  ->orWhere('user_id', $user->id);
        })
        ->orderBy('is_system', 'desc')
        ->orderBy('name')
        ->get();

        return response()->json([
            'success' => true,
            'data' => CalendarEventCategoryResource::collection($categories)
        ]);
    }
}
