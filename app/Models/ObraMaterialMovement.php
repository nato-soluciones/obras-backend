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
        'created_by_id',
    ];

    public function obraMaterial()
    {
        return $this->belongsTo(ObraMaterial::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
