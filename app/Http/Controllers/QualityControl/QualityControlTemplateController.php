<?php

namespace App\Http\Controllers\QualityControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\QualityControl\StoreQualityControlTemplateRequest;
use App\Http\Requests\QualityControl\UpdateQualityControlTemplateRequest;
use App\Models\QualityControl\QualityControlTemplate;
use Illuminate\Support\Facades\Log;

class QualityControlTemplateController extends Controller
{
    public function index()
    {
        return QualityControlTemplate::with('items')->get();
    }

    public function store(StoreQualityControlTemplateRequest $request)
    {
        $qualityControlTemplate = QualityControlTemplate::create(['name' => $request->input('name')]);
        // si se graba correctamente, guardar los items del template en QualityControlTemplateItem
        if ($qualityControlTemplate) {
            $qualityControlTemplate->items()->createMany($request->input('items'));
        }
        return $qualityControlTemplate;
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
