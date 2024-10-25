<?php

namespace App\Http\Services;

use App\Models\QualityControl\QualityControl;
use App\Models\QualityControl\QualityControlItem;
use App\Models\QualityControl\QualityControlTemplateItem;
use Exception;
use Illuminate\Support\Facades\Log;

class QualityControlService
{
    public function store($entityFather, $qualityControlTemplateId)
    {
        $qualityControlItems = QualityControlTemplateItem::where('template_id', $qualityControlTemplateId)->get();

        try {
            $qualityControlData = [
                'template_id' => $qualityControlTemplateId,
                'status' => 'UNCONTROLLED',
                'percentage' => 0,
                'made_by_id' => auth()->id(),
            ];

            $qualityControl = new QualityControl($qualityControlData);

            $entityFather->qualityControls()->save($qualityControl);

            // Crear los ítems del control de calidad
            foreach ($qualityControlItems as $itemData) {
                QualityControlItem::create([
                    'quality_control_id' => $qualityControl->id,
                    'template_item_id' => $itemData['id'],
                    'passed' => false,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('QUALITY SERVICE - Error en la transacción: ' . $e->getMessage());
            throw new Exception("Error al crear el control de calidad");
        }
    }

    public function show($entityFather, $qualityControlId)
    {
        try {
            return $entityFather->qualityControls()->where('id', $qualityControlId)->first();
        } catch (\Exception $e) {
            Log::error('QUALITY SERVICE - Error en la transacción: ' . $e->getMessage());
            throw new Exception("Error al mostrar el control de calidad");
        }
    }
}
