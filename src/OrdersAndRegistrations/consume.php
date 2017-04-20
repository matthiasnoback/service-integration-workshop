<?php
declare(strict_types=1);

use Bunny\Message;
use OrdersAndRegistrations\Application;
use Shared\RabbitMQ\Queue;
use function Common\Resilience\retry;

require __DIR__ . '/../../vendor/autoload.php';

$app = new Application();

retry(3, 1000, function () use ($app) {
    Queue::consume(
        function (Message $message) use ($app) {
            // do nothing (yet)
            if ($message->getHeader('message_type') === 'orders_and_registrations.place_order') {
                $app->placeOrder($message->content);
            }
        }
    );
});
