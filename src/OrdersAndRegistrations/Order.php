<?php
declare(strict_types=1);

namespace OrdersAndRegistrations;

use Common\EventSourcing\Aggregate\EventSourcedAggregate;
use Common\EventSourcing\Aggregate\EventSourcedAggregateCapabilities;

final class Order implements EventSourcedAggregate
{
    use EventSourcedAggregateCapabilities;

    private $conferenceId;
    private $numberOfTickets;

    public static function place(OrderId $orderId, ConferenceId $conferenceId, int $numberOfTickets): Order
    {
        $order = new static();

        $order->recordThat(new OrderPlaced($orderId, $conferenceId, $numberOfTickets));

        return $order;
    }

    private function whenOrderPlaced(OrderPlaced $event)
    {
        $this->id = $event->orderId();
        $this->conferenceId = $event->conferenceId();
        $this->numberOfTickets = $event->numberOfTickets();
    }
}
