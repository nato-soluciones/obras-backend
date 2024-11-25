<?php

namespace App\Models\QualityControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityControl extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'template_id',
        'status',
        'percentage',
        'comments',
        'required_reverification',
        'made_by_id',
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(QualityControlItem::class);
    }

    public function template()
    {
        return $this->belongsTo(QualityControlTemplate::class, 'template_id');
    }
}
