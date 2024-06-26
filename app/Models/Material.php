<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'measurement_unit_id',
    ];


    public function movements()
    {
        return $this->hasMany(ObraMaterialMovement::class);
    }
}
