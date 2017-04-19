<?php
declare(strict_types=1);

use Bunny\Message;
use OrdersAndRegistrations\Application;
use function Common\CommandLine\line;
use function Common\CommandLine\make_green;
use function Common\CommandLine\stdout;
use Shared\RabbitMQ\Queue;
use function Common\Resilience\retry;

require __DIR__ . '/../../vendor/autoload.php';

$app = new Application();

stdout(line(make_green('Waiting...')));

retry(3, 1000, function () use ($app) {
    Queue::consume(
        function (Message $message) use ($app) {
            // do nothing (yet)
        }
    );
});
