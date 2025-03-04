<?php

namespace App\Services;

use App\Interfaces\EventStoreInterface;

class EventStore implements EventStoreInterface
{
    public function storeEvent($eventType, $aggregateType, $aggregateId, array $eventData)
    {
        return \App\Models\Event::create([
            'event_type' => $eventType,
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'event_data' => $eventData,
            'occurred_at' => now()
        ]);
    }
    
    public function getEventsByAggregateId($aggregateType, $aggregateId)
    {
        return \App\Models\Event::where('aggregate_type', $aggregateType)
            ->where('aggregate_id', $aggregateId)
            ->orderBy('occurred_at')
            ->get();
    }
}