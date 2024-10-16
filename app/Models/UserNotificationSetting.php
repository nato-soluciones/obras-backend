<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'notification_setting_id', 
        'app_notifications', 
        'email_notifications'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notificationSetting()
    {
        return $this->belongsTo(NotificationSetting::class);
    }
}
