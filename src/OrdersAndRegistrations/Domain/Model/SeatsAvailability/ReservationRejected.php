<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Domain\Model\SeatsAvailability;

use OrdersAndRegistrations\ConferenceId;

final class ReservationRejected
{
    /**
     * @var ConferenceId
     */
    private $conferenceId;

    /**
     * @var ReservationId
     */
    private $reservationId;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(ConferenceId $conferenceId, ReservationId $reservationId, int $quantity)
    {
        $this->conferenceId = $conferenceId;
        $this->reservationId = $reservationId;
        $this->quantity = $quantity;
    }

    public function conferenceId()
    {
        return $this->conferenceId;
    }

    public function reservationId(): ReservationId
    {
        return $this->reservationId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }
}
