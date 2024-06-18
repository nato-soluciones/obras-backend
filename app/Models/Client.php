<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'business_name',
        'person_type',
        'address',
        'city',
        'state',
        'zip',
        'email',
        'phone',
        'cuit',
        'condition',
        'comments',
        'status'
    ];

    // protected static function boot()
    // {
    //     parent::boot();
    
    //     static::creating(function ($client) {
    //         do {
    //             $randomCode = Str::upper(strval(random_int(1000, 9999)));
    //         } while (self::where('code', $randomCode)->exists());
    
    //         $client->code = $randomCode;
    //     });
    // }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function currentAccounts()
    {
        return $this->hasMany(CurrentAccount::class, 'entity_id', 'id')->where('entity_type', 'CLIENT');
    }
}
