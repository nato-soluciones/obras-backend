<?php

namespace App\Http\Services;

use App\Models\NotificationSetting;
use App\Models\UserNotificationSetting;

class NotificationSettingsService
{
  public function getUserNotificationSettings($userId)
  {
    // Obtener todas las configuraciones de notificaciones disponibles
    $notificationSettings = NotificationSetting::all();

    // Obtener las configuraciones de notificaciones del usuario
    $userNotificationSettings = UserNotificationSetting::where('user_id', $userId)
      ->get()
      ->keyBy('notification_setting_id'); // parecido a array_column

    // Agrupar las configuraciones de notificaciones por grupo
    $formattedSettings = $notificationSettings->groupBy('group')->map(function ($groupSettings) use ($userNotificationSettings) {
      return [
        'group' => $groupSettings->first()->group,
        'notificationItems' => $groupSettings->map(function ($setting) use ($userNotificationSettings) {
          $userSetting = $userNotificationSettings->get($setting->id);

          return [
            'id' => $setting->id,
            'title' => $setting->title,
            'description' => $setting->description,
            'app_notifications' => $userSetting ? $userSetting->app_notifications : false,
            'email_notifications' => $userSetting ? $userSetting->email_notifications : false,
          ];
        })->values(),
      ];
    })->values();


    return $formattedSettings;
  }

  public function updateUserNotificationSettings($userId, $settings)
  {
    foreach ($settings as $setting) {
      UserNotificationSetting::updateOrCreate(
        ['user_id' => $userId, 'notification_setting_id' => $setting['id']],
        [
          'app_notifications' => $setting['app_notifications'],
          'email_notifications' => $setting['email_notifications']
        ]
      );
    }
  }
}
