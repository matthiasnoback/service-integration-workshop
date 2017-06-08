<?php
declare(strict_types=1);

namespace OrdersAndRegistrations;

use Common\EventDispatcher\EventDispatcher;
use Common\EventSourcing\Aggregate\Repository\EventSourcedAggregateRepository;
use Common\EventSourcing\EventStore\EventStore;
use Common\EventSourcing\EventStore\Storage\DatabaseStorageFacility;
use Common\Persistence\Database;
use NaiveSerializer\JsonSerializer;
use NaiveSerializer\Serializer;

final class Application
{
    public function placeOrderController(): void
    {
        $requestBody = file_get_contents('php://input');

        $command = Serializer::deserialize(PlaceOrder::class, $requestBody);

        $order = Order::place(
            OrderId::fromString($command->orderId),
            ConferenceId::fromString($command->conferenceId),
            (int)$command->numberOfTickets
        );

        Database::persist($order);

        $email = \Swift_Message::newInstance()
            ->setTo(['noreply@mywebsite.com'])
            ->setFrom(['noreply@mywebsite.com'])
            ->setSubject('Thanks for your order')
            ->setBody('Test');

        $this->mailer()->send($email);

        header('Content-Type: text/plain', true, 200);
        exit;
    }

    public function whenOrderPlaced(OrderPlaced $event)
    {
        // respond to OrderPlaced event (only when you're at assignment 03!)
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
}
