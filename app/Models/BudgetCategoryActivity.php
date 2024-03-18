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
    public function category(){
        return $this->belongsTo(BudgetCategory::class);
    }
}
