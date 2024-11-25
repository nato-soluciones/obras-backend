<?php

namespace App\Models\QualityControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityControlTemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'name'
    ];

    protected $hidden = [
        'template_id',
        'created_at',
        'updated_at'
    ];

    public function qualityControlTemplate()
    {
        return $this->belongsTo(QualityControlTemplate::class, 'template_id');
    }
}
