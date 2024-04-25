<?php

namespace App\Http\Controllers;

use App\Models\ObraStage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ObraStageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $obraId)
    {
        $incomes = ObraStage::where('obra_id', $obraId)->get();
        return response($incomes, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->merge(['progress' => 0]);
        $request->merge(['created_by_id' => auth()->user()->id]);

        $obraStage = ObraStage::create($request->all());
        return response($obraStage, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($obraId, $stageId): Response
    {
        $obraStage = ObraStage::find($stageId);
        return response($obraStage, 200);
    }


    /**
     * Show the form for update the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($obraId, $stageId)
    {
        $obraStage = ObraStage::find($stageId);
        if (!$obraStage) {
            return response()->json(['error' => 'Registro no encontrado'], 404);
        }

        $obraStage->update(request()->all());
        return response($obraStage, 200);
    }
}
