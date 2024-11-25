<?php

namespace App\Models\QualityControl;

use Illuminate\Database\Eloquent\Model;

class QualityControlItem extends Model
{
    protected $fillable = [
        'quality_control_id',
        'template_item_id',
        'passed',
    ];

    public function qualityControl()
    {
        return $this->belongsTo(QualityControl::class);
    }

    public function templateItem()
    {
        return $this->belongsTo(QualityControlTemplateItem::class, 'template_item_id');
    }
}
