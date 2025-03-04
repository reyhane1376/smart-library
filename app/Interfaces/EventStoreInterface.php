<?php

namespace App\Interfaces;


interface EventStoreInterface
{
    public function storeEvent($eventType, $aggregateType, $aggregateId, array $eventData);
    public function getEventsByAggregateId($aggregateType, $aggregateId);
}