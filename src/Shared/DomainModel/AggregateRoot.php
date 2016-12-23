<?php
declare(strict_types=1);

namespace Shared\DomainModel;

trait AggregateRoot
{
    private $events = [];

    protected function recordThat(PublishableDomainEvent $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return PublishableDomainEvent[]
     */
    final public function recordedEvents()
    {
        return $this->events;
    }
}
