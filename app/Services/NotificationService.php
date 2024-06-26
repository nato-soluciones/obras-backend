<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{

  public function create(int $user_id, string $title, string $description, string $priority = 'baja')
  {
    $priorities = ['baja', 'media', 'alta'];
    if (!in_array(strtolower($priority), $priorities)) return;

    $notification = [
      'user_id'     => $user_id,
      'title'       => $title,
      'description' => $description,
      'priority'    => strtolower($priority),
      'is_read'     => false,
      'date'        => date('Y-m-d'),
    ];

    Notification::create($notification);
  }
}
