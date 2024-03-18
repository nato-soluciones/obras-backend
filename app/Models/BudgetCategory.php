<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCategory extends Model
{
    use HasFactory;
    
    protected $table = 'budgets_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'total',
        'budget_id',
    ];

    public function budget(){
        return $this->belongsTo(Budget::class);
    }

    public function activities(){
        return $this->hasMany(BudgetCategoryActivity::class);
    }
}
