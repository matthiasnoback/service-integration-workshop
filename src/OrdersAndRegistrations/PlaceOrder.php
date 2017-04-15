<?php
declare(strict_types=1);

namespace OrdersAndRegistrations;

final class PlaceOrder
{
    /**
     * @var string
     */
    public $orderId;

    /**
     * @var string
     */
    public $conferenceId;

    /**
     * @var integer
     */
    public $numberOfTickets;
}
