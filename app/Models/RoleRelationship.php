<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleRelationship extends Model
{
    use HasFactory;
    
    public $incrementing = false;
    protected $primaryKey = ['user_role_id', 'functional_role_id'];
    protected $keyType = 'integer';

    protected $fillable = [
        'user_role_id',
        'functional_role_id',
    ];

    public function userRole()
    {
        return $this->belongsTo(Role::class, 'user_role_id');
    }

    public function functionalRole()
    {
        return $this->belongsTo(Role::class, 'functional_role_id');
    }
}
