<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolLocation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tools_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'location',
        'status',
        'image',
        'responsible',
        'comments',
        'created_by',
        'tool_id',
    ];

    

    /**
     * Get the tool that owns the location.
     */
    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }
}
