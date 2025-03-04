<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateScoreRequest;
use App\Interfaces\QueueServiceInterface;
use App\Interfaces\ScoreServiceInterface;
use App\Models\User;
use App\Services\ScoreService;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    protected $queueService;
    
    public function __construct(QueueServiceInterface $queueService)
    {
        $this->queueService = $queueService;
    }

    public function calculate(CalculateScoreRequest $request)
    {

        //TODO: implement access for get score users

        $user = User::findOrFail($request->user_id);

        $this->queueService->addToQueue('calculate_user_score', [
            'user_id' => $user->id
        ]);
        
    }
}
