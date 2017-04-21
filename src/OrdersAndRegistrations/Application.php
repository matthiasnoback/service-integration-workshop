<?php
declare(strict_types=1);

namespace OrdersAndRegistrations;

use Common\EventDispatcher\EventDispatcher;
use Common\EventSourcing\Aggregate\Repository\EventSourcedAggregateRepository;
use Common\EventSourcing\EventStore\EventStore;
use Common\EventSourcing\EventStore\Storage\DatabaseStorageFacility;
use NaiveSerializer\JsonSerializer;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\ReservationId;
use OrdersAndRegistrations\Domain\Model\SeatsAvailability\SeatsAvailability;
use Ramsey\Uuid\Uuid;

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

    public function whenOrderPlaced(OrderPlaced $event)
    {
        // Make a seat reservation:
        /** @var SeatsAvailability $seatsAvailability */
        $seatsAvailability = $this->seatsAvailabilityRepository()->getById((string)$event->conferenceId());

        $seatsAvailability->makeReservation(ReservationId::fromString((string)Uuid::uuid4()), $event->numberOfTickets());

        $this->seatsAvailabilityRepository()->save($seatsAvailability);

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
}
