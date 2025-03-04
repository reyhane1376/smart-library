<?php

namespace App\Interfaces;

use App\Models\User;

interface NotificationServiceInterface
{
    public function sendNotification(User $user, $type, $message);
    public function markAsRead($notificationId);
    public function retryFailedNotifications();
}