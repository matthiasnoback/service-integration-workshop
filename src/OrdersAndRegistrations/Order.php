<?php
declare(strict_types=1);

namespace OrdersAndRegistrations;

final class Order
{
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

    private function __construct(OrderId $orderId, ConferenceId $conferenceId, int $numberOfTickets)
    {
        $this->id = $orderId;
        $this->conferenceId = $conferenceId;
        $this->numberOfTickets = $numberOfTickets;
    }

    public static function place(OrderId $orderId, ConferenceId $conferenceId, int $numberOfTickets): Order
    {
        return new self($orderId, $conferenceId, $numberOfTickets);
    }

    public function id()
    {
        return $this->id;
    }
}
