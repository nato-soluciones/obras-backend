<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_id',
        'material_id',
        'quantity',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function movements()
    {
        return $this->hasMany(ObraMaterialMovement::class);
    }
}
