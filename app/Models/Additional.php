<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Additional extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'comments',
        'total',
        'total_cost',
        'obra_id',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'total' => 'float',
    //     'total_cost' => 'float',
    // ];

    protected static function booted()
    {
      static::creating(function($post) {
        if (empty($post->created_by)) {
          $post->created_by = Auth::id();
        }
      });
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function categories()
    {
        return $this->hasMany(AdditionalCategory::class);
    }
}
