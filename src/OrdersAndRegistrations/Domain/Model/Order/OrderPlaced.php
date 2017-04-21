<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Domain\Model\Order;

final class OrderPlaced
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var ConferenceId
     */
    private $conferenceId;

    /**
     * @var int
     */
    private $numberOfTickets;

    public function __construct(OrderId $orderId, ConferenceId $conferenceId, int $numberOfTickets)
    {
        $this->orderId = $orderId;
        $this->conferenceId = $conferenceId;
        $this->numberOfTickets = $numberOfTickets;
    }

    public function orderId(): OrderId
    {
        return $this->orderId;
    }

    public function conferenceId(): ConferenceId
    {
        return $this->conferenceId;
    }

    public function numberOfTickets(): int
    {
        return $this->numberOfTickets;
    }
}
