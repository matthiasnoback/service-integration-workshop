<?php
declare(strict_types=1);

namespace OrdersAndRegistrations;

use Common\EventDispatcher\EventDispatcher;
use Common\EventSourcing\Aggregate\Repository\EventSourcedAggregateRepository;
use Common\EventSourcing\EventStore\EventStore;
use Common\EventSourcing\EventStore\Storage\DatabaseStorageFacility;
use NaiveSerializer\JsonSerializer;
use OrdersAndRegistrations\Application\CommitSeatReservation;
use OrdersAndRegistrations\Application\ExpireOrder;
use OrdersAndRegistrations\Application\MakeSeatReservation;
use OrdersAndRegistrations\Application\MarkAsBooked;
use OrdersAndRegistrations\Application\OrderProcessManager;
use OrdersAndRegistrations\Application\PaymentReceived;
use OrdersAndRegistrations\Application\PlaceOrder;
use OrdersAndRegistrations\Application\RejectOrder;
use OrdersAndRegistrations\Domain\Model\Order\ConferenceId;
use OrdersAndRegistrations\Domain\Model\Order\Order;
use OrdersAndRegistrations\Domain\Model\Order\OrderId;
use OrdersAndRegistrations\Domain\Model\Order\OrderPlaced;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationAccepted;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationId;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationRejected;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\SeatsAvailability;

final class Application
{
    public function placeOrder(PlaceOrder $command): void
    {
        $order = Order::place(
            OrderId::fromString($command->orderId),
            ConferenceId::fromString($command->conferenceId),
            (int)$command->numberOfTickets
        );

        $this->orderRepository()->save($order);
    }

    public function rejectOrder(RejectOrder $command): void
    {
        /** @var Order $order */
        $order = $this->orderRepository()->getById($command->orderId);

        $order->reject();

        $this->orderRepository()->save($order);
    }

    public function expireOrder(ExpireOrder $command): void
    {
        /** @var Order $order */
        $order = $this->orderRepository()->getById($command->orderId);

        $order->expire();

        $this->orderRepository()->save($order);
    }

    public function markAsBooked(MarkAsBooked $command)
    {
        /** @var Order $order */
        $order = $this->orderRepository()->getById($command->orderId);

        $order->markAsBooked();

        $this->orderRepository()->save($order);
    }

    public function makeReservation(MakeSeatReservation $command): void
    {
        /** @var SeatsAvailability $seatsAvailability */
        $seatsAvailability = $this->seatsAvailabilityRepository()->getById($command->conferenceId);

        $seatsAvailability->makeReservation(ReservationId::fromString($command->reservationId), $command->quantity);

        $this->seatsAvailabilityRepository()->save($seatsAvailability);
    }

    public function whenOrderPlaced(OrderPlaced $event)
    {
        // Send a confirmation email:
        $email = \Swift_Message::newInstance()
            ->setTo(['noreply@mywebsite.com'])
            ->setFrom(['noreply@mywebsite.com'])
            ->setSubject('Thanks for your order')
            ->setBody('Test');

        $this->mailer()->send($email);
    }

    private function orderRepository(): EventSourcedAggregateRepository
    {
        static $orderRepository;

        if ($orderRepository === null) {
            $orderRepository = $orderRepository ?? new EventSourcedAggregateRepository(
                    new EventStore(
                        new DatabaseStorageFacility(),
                        $this->eventDispatcher(),
                        new JsonSerializer()
                    ),
                    Order::class
                );
        }

        return $orderRepository;
    }

    private function eventDispatcher(): EventDispatcher
    {
        static $eventDispatcher;

        if ($eventDispatcher === null) {
            $eventDispatcher = new EventDispatcher();

            $eventDispatcher->registerSubscriber(OrderPlaced::class, [$this, 'whenOrderPlaced']);

            $eventDispatcher->registerSubscriber(
                OrderPlaced::class,
                [$this->orderProcessManager(), 'whenOrderPlaced']
            );
            $eventDispatcher->registerSubscriber(
                ReservationAccepted::class,
                [$this->orderProcessManager(), 'whenReservationAccepted']
            );
            $eventDispatcher->registerSubscriber(
                ReservationRejected::class,
                [$this->orderProcessManager(), 'whenReservationRejected']
            );
            $eventDispatcher->registerSubscriber(
                PaymentReceived::class,
                [$this->orderProcessManager(), 'whenPaymentReceived']
            );
        }

        return $eventDispatcher;
    }

    private function mailer(): \Swift_Mailer
    {
        static $mailer;

        if ($mailer === null) {
            $transport = \Swift_SmtpTransport::newInstance('mailcatcher', 1025);
            $mailer = \Swift_Mailer::newInstance($transport);
        }

        return $mailer;
    }

    public function whenConferenceCreated($data)
    {
        $seatsAvailability = SeatsAvailability::create(ConferenceId::fromString($data->id), $data->availableTickets);

        $this->seatsAvailabilityRepository()->save($seatsAvailability);
    }

    private function seatsAvailabilityRepository(): EventSourcedAggregateRepository
    {
        static $seatsAvailabilityRepository;

        if ($seatsAvailabilityRepository === null) {
            $seatsAvailabilityRepository = $seatsAvailabilityRepository ?? new EventSourcedAggregateRepository(
                    new EventStore(
                        new DatabaseStorageFacility(),
                        $this->eventDispatcher(),
                        new JsonSerializer()
                    ),
                    SeatsAvailability::class
                );
        }

        return $seatsAvailabilityRepository;
    }

    private function orderProcessManager(): OrderProcessManager
    {
        static $orderProcessManager;

        if ($orderProcessManager === null) {
            $orderProcessManager = new OrderProcessManager($this);
        }

        return $orderProcessManager;
    }

    public function consumePaymentReceived($data): void
    {
        $this->eventDispatcher()->dispatch(new PaymentReceived(OrderId::fromString($data->orderId)));
    }

    public function commitSeatReservation(CommitSeatReservation $command)
    {
        /** @var SeatsAvailability $seatsAvailability */
        $seatsAvailability = $this->seatsAvailabilityRepository()->getById($command->conferenceId);

        $seatsAvailability->commitReservation(ReservationId::fromString($command->reservationId));

        $this->seatsAvailabilityRepository()->save($seatsAvailability);
    }
}
