<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Application;

use OrdersAndRegistrations\Domain\Model\Order\OrderId;

final class OrderState
{
    const NOT_STARTED = 'NOT_STARTED';
    const AWAITING_RESERVATION_CONFIRMATION = 'AWAITING_RESERVATION_CONFIRMATION';
    const AWAITING_PAYMENT = 'AWAITING_PAYMENT';
    const COMPLETED = 'COMPLETED';
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var string
     */
    private $state;

    public function id(): string
    {
        return (string)$this->orderId;
    }

    public static function awaitReservationConfirmation(OrderId $orderId)
    {
        $orderState = new OrderState();

        $orderState->orderId = $orderId;
        $orderState->state = self::AWAITING_RESERVATION_CONFIRMATION;

        return $orderState;
    }

    public function awaitPayment(): void
    {
        $this->state = self::AWAITING_PAYMENT;
    }

    public function complete(): void
    {
        $this->state = self::COMPLETED;
    }

    public function isCompleted(): bool
    {
        return $this->state === self::COMPLETED;
    }

    public function isAwaitingPayment()
    {
        return $this->state === self::AWAITING_PAYMENT;
    }

    public function isAwaitingReservationConfirmation()
    {
        return $this->state = self::AWAITING_RESERVATION_CONFIRMATION;
    }
}
