<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateUserScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    public function handle(\App\Interfaces\ScoreServiceInterface $scoreService)
    {
        $userId = $this->data['user_id'];
        $user = User::findOrFail($userId);
        
        // Calculate and update user score
        $scoreService->calculateUserScore($user);
        
        return true;
    }
}
