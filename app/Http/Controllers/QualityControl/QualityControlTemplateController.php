<?php

namespace App\Http\Controllers\QualityControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\QualityControl\StoreQualityControlTemplateRequest;
use App\Http\Requests\QualityControl\UpdateQualityControlTemplateRequest;
use App\Models\QualityControl\QualityControlTemplate;

class QualityControlTemplateController extends Controller
{
    public function index()
    {
        return QualityControlTemplate::all();
    }

    public function store(StoreQualityControlTemplateRequest $request)
    {
        //
    }

    public function show(QualityControlTemplate $qualityControlTemplate)
    {
        //
    }

    public function update(UpdateQualityControlTemplateRequest $request, QualityControlTemplate $qualityControlTemplate)
    {
        //
    }

    public function destroy(QualityControlTemplate $qualityControlTemplate)
    {
        //
    }
}
