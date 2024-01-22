<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($client) {
            do {
                $randomCode = Str::upper(strval(random_int(1000, 9999)));
            } while (self::where('code', $randomCode)->exists());
    
            $client->code = $randomCode;
        });
    }

    public function users() {
        return $this->hasMany(User::class);
    }
}
