<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCategoryActivity extends Model
{
    use HasFactory;

    protected $table = 'budgets_categories_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'provider_id',
        'unit',
        'unit_cost',
        'profit',
        'unit_price',
        'quantity',
        'subtotal',
        'budget_category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_cost' => 'float',
        'unit_price' => 'float',
        'subtotal' => 'float',
        'quantity' => 'float',
    ];

    public function category(){
        return $this->belongsTo(BudgetCategory::class);
    }
}
