<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraMaterialMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_material_id',
        'date',
        'movement_type',
        'measurement_unit_id',
        'quantity',
        'description',
        'observation',
    ];

    public function obraMaterial()
    {
        return $this->belongsTo(ObraMaterial::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
}
