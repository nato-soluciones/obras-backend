<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraStageSubStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'progress',
        'start_date',
        'end_date',
        'obra_stage_id',
        'created_by_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    public function obraStage()
    {
        return $this->belongsTo(ObraStage::class);
    }

    public function tasks()
    {
        return $this->hasMany(ObraStageSubStageTask::class);
    }
}
