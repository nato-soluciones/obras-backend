<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentAccount extends Model
{
    use HasFactory;

    // valid entity types
    const ENTITY_TYPES = ['CLIENT', 'PROVIDER'];
    // valid currencies
    const CURRENCIES = ['USD', 'ARS'];

    protected $fillable = [
        'entity_type',
        'entity_id',
        'project_id',
        'currency',
        'balance',
    ];

    public function movements()
    {
        return $this->hasMany(CurrentAccountMovement::class, 'current_account_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'entity_id', 'id');
    }

    public function provider()
    {
        return $this->belongsTo(Contractor::class, 'entity_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Obra::class, 'project_id', 'id');
    }
}
