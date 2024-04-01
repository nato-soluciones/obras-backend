<?php

namespace App\Http\Controllers;

use App\Models\ObraDailyLogTag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ObraDailyLogTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $response = ObraDailyLogTag::where('active', true)->select('id', 'name', 'color')->get();
        return response($response, 200);
    }



    /**
     * Display the specified resource.
     */
    public function show(int $id): Response
    {
        $response = ObraDailyLogTag::findOrFail($id);
        return response($response, 200);
    }


}
