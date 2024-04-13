<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraStageTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'responsible_id',
        'description',
        'is_completed',
        'obra_stage_id',
        'created_by_id',
    ];

    public function obraStage()
    {
        return $this->belongsTo(ObraStage::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
