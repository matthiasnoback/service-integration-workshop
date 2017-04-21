<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Application;

use OrdersAndRegistrations\Domain\Model\Order\OrderId;

final class PaymentReceived
{
    private $orderId;

    public function __construct(OrderId $orderId)
    {
        $this->orderId = $orderId;
    }

    public function orderId(): OrderId
    {
        return $this->orderId;
    }
}
