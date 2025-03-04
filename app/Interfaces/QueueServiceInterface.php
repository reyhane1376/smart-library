<?php

namespace App\Interfaces;


interface QueueServiceInterface
{
    public function addToQueue($jobName, array $data);
}