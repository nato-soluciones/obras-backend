<?php

namespace App\Models\Company;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_date',
        'period',
        'description',
        'amount',
        'payment_status',
        'payment_date',
        'category_id',
        'responsible_id',
        'created_by_id',
    ];

    public function category()
    {
        return $this->belongsTo(CompanyCostCategory::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class);
    }
}
