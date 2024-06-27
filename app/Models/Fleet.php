<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Fleet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'brand',
        'model',
        'value',
        'mileage',
        'initial_mileage',
        'domain',
        'image',
        'purchase_date',
        'vtv_expiration',
        'next_plate_payment',
        'status',
        'type',
    ];
    
    protected $appends = ['type_name'];

    const VEHICLE_TYPES = [
        'CAR' => 'Auto',
        'PICKUP' => 'Camioneta',
        'TRUCK' => 'Camión',
        'MOTORCYCLE' => 'Moto',
        'UTILITY' => 'Utilitario',
        'OTHER' => 'Otro'
    ];

    public function getTypeNameAttribute() // el atributo se llama type_name, get{AttributeName}Attribute
    {
        return self::VEHICLE_TYPES[$this->type] ?? 'Desconocido';
    }
    /**
     * Get the movements for the fleet.
     */
    public function movements()
    {
        return $this->hasMany(FleetMovement::class);
    }

    /**
     * Get the documents for the fleet.
     */
    public function documents()
    {
        return $this->hasMany(FleetDocument::class);
    }

    /**
     * Get the last movement for the fleet (fecha pasada).
     */
    public function last_movement()
    {
        return $this->hasOne(FleetMovement::class)
            ->where('date', '<=', Carbon::now())
            ->where('type', '=', 'MAINTENANCE')
            ->latest('date');
    }

    /**
     * Get the next movement for the fleet.
     */
    public function next_movement()
    {
        return $this->hasOne(FleetMovement::class)
            ->where('date', '>=', Carbon::now())
            ->where('type', '=', 'MAINTENANCE')
            ->oldest('date');
    }
}
