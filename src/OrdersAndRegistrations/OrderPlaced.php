<?php

namespace OrdersAndRegistrations;

use Ramsey\Uuid\UuidInterface;
use Shared\DomainModel\PublishableDomainEvent;

final class OrderPlaced implements PublishableDomainEvent
{
    private $orderId;
    private $conferenceId;
    private $numberOfTickets;

    public function __construct(UuidInterface $orderId, UuidInterface $conferenceId, int $numberOfTickets)
    {
        $this->orderId = $orderId;
        $this->conferenceId = $conferenceId;
        $this->numberOfTickets = $numberOfTickets;
    }

    public function eventData() : array
    {
        return array_merge(
            get_object_vars($this),
            ['_type' => 'orders_and_registrations.order_placed']
        );
    }
}
