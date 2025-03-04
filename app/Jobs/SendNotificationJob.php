<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function handle()
    {
        $notificationId = $this->data['notification_id'];
        $notification = Notification::findOrFail($notificationId);
        
        // Here we would implement the actual notification sending
        // This could be an email, SMS, push notification, etc.
        
        // For this example, we'll just mark it as sent
        $notification->sent_at = now();
        $notification->save();
        
        return true;
    }
    
    public function failed(\Exception $exception)
    {
        $notificationId = $this->data['notification_id'];
        $notification = Notification::find($notificationId);
        
        if ($notification) {
            $notification->retry_count += 1;
            $notification->save();
        }
    }
}
