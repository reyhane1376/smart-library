<?php

namespace App\Services;

use App\Interfaces\QueueServiceInterface;

class QueueService implements QueueServiceInterface
{
    public function addToQueue($jobName, array $data)
    {
        // We can use Laravel's built-in queue system
        switch ($jobName) {
            case 'send_notification':
                \App\Jobs\SendNotificationJob::dispatch($data)->onQueue('notifications');
                break;
            case 'calculate_user_score':
                \App\Jobs\CalculateUserScoreJob::dispatch($data)->onQueue('scoring');
                break;
            default:
                throw new \Exception("Unknown job: {$jobName}");
        }
        
        return true;
    }
}