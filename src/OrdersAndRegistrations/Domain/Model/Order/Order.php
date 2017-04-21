<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Domain\Model\Order;

use Common\EventSourcing\Aggregate\EventSourcedAggregate;
use Common\EventSourcing\Aggregate\EventSourcedAggregateCapabilities;

final class Order implements EventSourcedAggregate
{
    use EventSourcedAggregateCapabilities;

    private const NEW = 'new';
    private const BOOKED = 'booked';
    private const REJECTED = 'rejected';
    private const EXPIRED = 'expired';

    /**
     * @var OrderId
     */
    private $id;

    /**
     * @var ConferenceId
     */
    private $conferenceId;

    /**
     * @var int
     */
    private $numberOfTickets;

    /**
     * @var string
     */
    private $state;

    public static function place(OrderId $orderId, ConferenceId $conferenceId, int $numberOfTickets): Order
    {
        $order = new static();

        $order->recordThat(new OrderPlaced($orderId, $conferenceId, $numberOfTickets));

        return $order;
    }

    public function markAsBooked()
    {
        if ($this->state !== self::NEW) {
            throw new \LogicException();
        }

        $this->recordThat(new MarkedAsBooked($this->id));
    }

    private function whenMarkedAsBooked(MarkedAsBooked $event): void
    {
        $this->state = self::BOOKED;
    }

    public function reject()
    {
        if (!in_array($this->state, [self::NEW, self::BOOKED])) {
            throw new \LogicException();
        }

        $this->recordThat(new OrderRejected($this->id));
    }

    private function whenOrderRejected(OrderRejected $event): void
    {
        $this->state = self::REJECTED;
    }

    public function expire(): void
    {
        if (!in_array($this->state, [self::NEW, self::BOOKED])) {
            throw new \LogicException();
        }

        $this->recordThat(new OrderExpired($this->id));
    }

    private function whenOrderExpired(OrderExpired $event): void
    {
        $this->state = self::EXPIRED;
    }

    private function whenOrderPlaced(OrderPlaced $event): void
    {
        $this->id = $event->orderId();
        $this->conferenceId = $event->conferenceId();
        $this->numberOfTickets = $event->numberOfTickets();
        $this->state = self::NEW;
    }

    public function id(): string
    {
        return (string)$this->id;
    }
}
