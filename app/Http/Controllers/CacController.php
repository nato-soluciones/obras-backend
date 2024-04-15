<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Cac;

class CacController extends Controller
{
    /**
     * List all cacs
     *
     * @return Response
     */
    public function index(): Response
    {
        $cacs = Cac::orderBy('period', 'desc')->get();
        return response($cacs);
    }

    /**
     * Creates an cac
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $last = Cac::where('state', $request->state)
            ->where('period', '<', $request->period)
            ->orderBy('period', 'desc')
            ->first();

        if ($last) {
            $previousValue = $last->value;
            $newValue = $request->value;

            $variation = $previousValue != 0 ? intval((($newValue - $previousValue) / $previousValue) * 100) : 0;
            $request->merge(['inter_month_variation' => $variation]);
        }

        $cac = Cac::create($request->all());
        
        return response($cac, 201);
    }

    /**
     * Delete an cac by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $cac = Cac::find($id);
        $cac->delete();

        return response(['message' => 'CAC deleted'], 204);;
    }
}
