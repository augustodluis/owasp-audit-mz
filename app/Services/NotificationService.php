<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function send(User $user, string $type, string $message): Notification
    {
        return $user->notifications()->create([
            'type'      => $type,
            'message'   => $message,
            'read_flag' => false,
        ]);
    }

    public function pending(User $user)
    {
        return $user->notifications()
            ->where('read_flag', false)
            ->latest()
            ->get();
    }
}
