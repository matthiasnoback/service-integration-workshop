<?php

namespace OrdersAndRegistrations;

use Shared\DomainModel\AggregateRoot;
use Ramsey\Uuid\UuidInterface;
use Shared\Persistence\CanBePersisted;

final class Order implements CanBePersisted
{
    use AggregateRoot;

    private $orderId;
    private $conferenceId;
    private $numberOfTickets;

    private function __construct(UuidInterface $orderId, UuidInterface $conferenceId, int $numberOfTickets)
    {
        $this->orderId = $orderId;
        $this->conferenceId = $conferenceId;
        $this->numberOfTickets = $numberOfTickets;

        $this->recordThat(new OrderPlaced($orderId, $conferenceId, $numberOfTickets));
    }

    public static function place(UuidInterface $orderId, UuidInterface $conferenceId, int $numberOfTickets) : Order
    {
        return new self($orderId, $conferenceId, $numberOfTickets);
    }

    public function id() : UuidInterface
    {
        return $this->orderId;
    }
}
