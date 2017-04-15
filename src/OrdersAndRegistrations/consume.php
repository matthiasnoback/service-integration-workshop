<?php
declare(strict_types=1);

use Bunny\Message;
use NaiveSerializer\Serializer;
use OrdersAndRegistrations\Application;
use OrdersAndRegistrations\PlaceOrder;
use function Common\CommandLine\line;
use function Common\CommandLine\make_green;
use function Common\CommandLine\make_red;
use function Common\CommandLine\make_yellow;
use function Common\CommandLine\stdout;
use Shared\RabbitMQ\Queue;
use function Common\Resilience\retry;

require __DIR__ . '/../../vendor/autoload.php';

$app = new Application();

stdout(line(make_green('Waiting')));
pcntl_signal(SIGTERM, function () {
    stdout(line(make_red('SIGTERM')));
    exit(0);
});

retry(3, 1000, function () use ($app) {
    Queue::consume('commands', 'orders_and_registrations.commands', 'orders_and_registrations.#',
        function (Message $message) use ($app) {
            if ($message->getHeader('message_type') === 'orders_and_registrations.place_order') {
                $command = Serializer::deserialize(PlaceOrder::class, $message->content);

                stdout(line(make_yellow('Handling PlaceOrder command')));

                $app->handlePlaceOrder($command);

                stdout(line(make_green('Done')));
            }
        }
    );
});
