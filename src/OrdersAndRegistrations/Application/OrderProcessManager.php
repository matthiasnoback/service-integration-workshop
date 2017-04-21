<?php
declare(strict_types=1);

namespace OrdersAndRegistrations\Application;

use Common\Persistence\Database;
use OrdersAndRegistrations\Application;
use OrdersAndRegistrations\Domain\Model\Order\OrderPlaced;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationAccepted;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationRejected;

final class OrderProcessManager
{
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function whenOrderPlaced(OrderPlaced $event): void
    {
        /*
         * Transition to state: "awaiting reservation confirmation"
         */
        $orderState = OrderState::awaitReservationConfirmation($event->conferenceId(), $event->orderId());
        Database::persist($orderState);

        /*
         * Send command: "make seat reservation"
         */
        $command = new MakeSeatReservation();
        $command->conferenceId = (string)$event->conferenceId();
        $command->reservationId = (string)$event->orderId();
        $command->quantity = $event->numberOfTickets();

        $this->application->makeReservation($command);
    }

    public function whenReservationAccepted(ReservationAccepted $event): void
    {
        /** @var OrderState $orderState*/
        $orderState = Database::retrieve(OrderState::class, (string)$event->reservationId());

        if (!$orderState->isAwaitingReservationConfirmation()) {
            throw new InvalidOperation();
        }

        /*
         * Transition to state: awaiting payment
         */
        $orderState->awaitPayment();
        Database::persist($orderState);

        /*
         * Send command: MarkAsBooked
         */
        $command = new MarkAsBooked();
        $command->orderId = (string)$event->reservationId();
        $this->application->markAsBooked($command);

        /*
         * Send delayed command: ExpireOrder (in 15 minutes)
         */
        // TODO
    }

    public function whenReservationRejected(ReservationRejected $event): void
    {
        /** @var OrderState $orderState*/
        $orderState = Database::retrieve(OrderState::class, (string)$event->reservationId());

        if (!$orderState->isAwaitingReservationConfirmation()) {
            throw new InvalidOperation();
        }

        /*
         * Transition to state: "completed"
         */
        $orderState->complete();
        Database::persist($orderState);

        /*
         * Send command: "reject order"
         */
        $command = new RejectOrder();
        $command->orderId = (string)$event->reservationId();
        $this->application->rejectOrder($command);
    }

    public function whenPaymentReceived(PaymentReceived $event): void
    {
        /** @var OrderState $orderState*/
        $orderState = Database::retrieve(OrderState::class, (string)$event->orderId());

        if (!$orderState->isAwaitingPayment()) {
            throw new InvalidOperation();
        }

        /*
         * Transition to state: "completed"
         */
        $orderState->complete();
        Database::persist($orderState);

        /*
         * Send command: "commit seat reservation"
         */
        $command = new CommitSeatReservation();
        $command->reservationId = (string)$event->orderId();
        $command->conferenceId = (string)$orderState->conferenceId();

        $this->application->commitSeatReservation($command);
    }
}
