<?php

use OrdersAndRegistrations\Order;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;
use function Shared\CommandLine\line;
use function Shared\CommandLine\make_green;
use function Shared\CommandLine\stdout;
use Shared\RabbitMQ\Exchange;
use Shared\RabbitMQ\Queue;
use function Shared\Resilience\retry;

require __DIR__ . '/../../vendor/autoload.php';

retry(30, 1000, function () {
    Queue::consume('commands', 'orders_and_registrations.commands', 'orders_and_registrations.#',
        function (AMQPMessage $AMQPMessage) {
            $command = json_decode($AMQPMessage->body, true);

            if ($command['_type'] == 'orders_and_registrations.place_order') {
                stdout(line('Handling "', $command['_type'], '"'));

                handle_place_order($command);

                stdout(line(make_green('Done')));
            }
        }
    );
});

function handle_place_order(array $command)
{
    $order = Order::place(
        Uuid::fromString($command['order_id']), 
        Uuid::fromString($command['conference_id']),
        (int)$command['number_of_tickets']
    );

    // TODO: persist $order

    foreach ($order->recordedEvents() as $event) {
        Exchange::publishEvent($event->eventData());
    }

//
//    $makeSeatReservation = [
//        '_type' => 'orders_and_registrations'
//    ];
//
//    Exchange::publishCommand([]);
}
