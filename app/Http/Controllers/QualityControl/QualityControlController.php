<?php

namespace App\Http\Controllers\QualityControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\QualityControl\StoreQualityControlRequest;
use App\Http\Requests\QualityControl\UpdateQualityControlRequest;
use App\Models\QualityControl\QualityControl;

class QualityControlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQualityControlRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(QualityControl $qualityControl)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QualityControl $qualityControl)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQualityControlRequest $request, QualityControl $qualityControl)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QualityControl $qualityControl)
    {
        //
    }
}
