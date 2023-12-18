<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'image',
        'name',
        'company',
        'address',
        'department',
        'district',
        'city',
        'state',
        'email',
        'phone',
        'cuit',
        'condition',
        'alicuota',
        'invoice',
        'comments',
        'status'
    ];

    public function users() {
        return $this->hasMany(User::class);
    }
}
