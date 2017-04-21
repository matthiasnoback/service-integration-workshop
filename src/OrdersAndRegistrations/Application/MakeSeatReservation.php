<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Application;

final class MakeSeatReservation
{
    /**
     * @var string
     */
    public $conferenceId;

    /**
     * @var string
     */
    public $reservationId;

    /**
     * @var int
     */
    public $quantity;
}
