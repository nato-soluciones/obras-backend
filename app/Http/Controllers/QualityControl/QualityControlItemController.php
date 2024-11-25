<?php

namespace App\Http\Controllers\QualityControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\QualityControl\StoreQualityControlItemRequest;
use App\Http\Requests\QualityControl\UpdateQualityControlItemRequest;
use App\Models\QualityControl\QualityControlItem;

class QualityControlItemController extends Controller
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
    public function store(StoreQualityControlItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(QualityControlItem $qualityControlItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QualityControlItem $qualityControlItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQualityControlItemRequest $request, QualityControlItem $qualityControlItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QualityControlItem $qualityControlItem)
    {
        //
    }
}
