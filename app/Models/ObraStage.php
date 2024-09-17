<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'description',
        'progress',
        'obra_percentage',
        'obra_id',
        'created_by_id',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    
    public function subStages()
    {
        return $this->hasMany(ObraStageSubStage::class);
    }

    public function tasks()
    {
        return $this->hasMany(ObraStageSubStageTask::class);
    }
}
