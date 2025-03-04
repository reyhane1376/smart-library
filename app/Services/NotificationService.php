<?php

namespace App\Services;

use App\Interfaces\NotificationServiceInterface;
use App\Interfaces\QueueServiceInterface;
use App\Models\User;

class NotificationService implements NotificationServiceInterface
{
    protected $queueService;
    
    public function __construct(QueueServiceInterface $queueService)
    {
        $this->queueService = $queueService;
    }
    
    public function sendNotification(User $user, $type, $message)
    {
        $notification = \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'is_read' => false,
            'retry_count' => 0,
            'sent_at' => null
        ]);
        
        // Add to queue for async processing
        $this->queueService->addToQueue('send_notification', [
            'notification_id' => $notification->id
        ]);
        
        return $notification;
    }
    
    public function markAsRead($notificationId)
    {
        $notification = \App\Models\Notification::findOrFail($notificationId);
        $notification->is_read = true;
        $notification->save();
        
        return $notification;
    }
    
    public function retryFailedNotifications()
    {
        $failedNotifications = \App\Models\Notification::whereNull('sent_at')
            ->where('retry_count', '<', 5) // Max 5 retries
            ->get();
            
        foreach ($failedNotifications as $notification) {
            $notification->retry_count += 1;
            $notification->save();
            
            // Add to queue for retry
            $this->queueService->addToQueue('send_notification', [
                'notification_id' => $notification->id
            ]);
        }
        
        return count($failedNotifications);
    }
}