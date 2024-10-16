<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraStageSubStageTaskEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'title',
        'description',
        'created_by_id',
        'obra_stage_sub_stage_task_id',
    ];

    public function obraStageSubStageTask()
    {
        return $this->belongsTo(ObraStageSubStageTask::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
