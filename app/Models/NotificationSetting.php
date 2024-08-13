<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group', 
        'title', 
        'description', 
        'app_notifications', 
        'email_notifications', 
        'user_id'
    ];

    public function userNotificationSettings()
    {
        return $this->hasMany(UserNotificationSetting::class);
    }
}
