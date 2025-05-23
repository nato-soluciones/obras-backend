<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreMovementType extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name', 'description'];

    protected $hidden = ['deleted_at'];

    public function concepts()
    {
        return $this->hasMany(StoreMovementConcept::class, 'movement_type_id');
    }
}
