<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreMovementType extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false; // Desactiva created_at y updated_at

    protected $fillable = ['name', 'description'];
}
