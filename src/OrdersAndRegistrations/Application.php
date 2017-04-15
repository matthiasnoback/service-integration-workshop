<?php
declare(strict_types=1);

namespace OrdersAndRegistrations;

use Common\EventDispatcher\EventDispatcher;
use Common\EventSourcing\Aggregate\Repository\EventSourcedAggregateRepository;
use Common\EventSourcing\EventStore\EventStore;
use Common\EventSourcing\EventStore\Storage\FlywheelStorageFacility;
use NaiveSerializer\JsonSerializer;
use Shared\RabbitMQ\Exchange;

final class Application
{
    public function handlePlaceOrder(PlaceOrder $command): void
    {
        $order = Order::place(
            OrderId::fromString($command->orderId),
            ConferenceId::fromString($command->conferenceId),
            (int)$command->numberOfTickets
        );

        $this->orderRepository()->save($order);
    }

    public function onOrderPlaced(OrderPlaced $event): void
    {
        Exchange::publishEvent('orders_and_registrations.order_placed', $event);
    }

    private function orderRepository(): EventSourcedAggregateRepository
    {
        static $orderRepository;

        if ($orderRepository === null) {
            $orderRepository = $orderRepository ?? new EventSourcedAggregateRepository(
                    new EventStore(
                        new FlywheelStorageFacility(__DIR__ . '/../../var/db/orders_and_registrations'),
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

            $eventDispatcher->registerSubscriber(OrderPlaced::class, [$this, 'onOrderPlaced']);

        }

        return $eventDispatcher;
    }
}
