<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Domain\Model\Order;

final class OrderExpired
{
    private $orderId;

    public function __construct(OrderId $orderId)
    {
        $this->orderId = $orderId;
    }

    public function orderId()
    {
        return $this->orderId;
    }
}
