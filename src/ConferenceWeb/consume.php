<?php
declare(strict_types=1);

use Bunny\Message;
use ConferenceWeb\Application;
use Shared\RabbitMQ\Queue;
use function Common\Resilience\retry;

require __DIR__ . '/../../vendor/autoload.php';

$app = new Application();

retry(3, 1000, function () use ($app) {
    Queue::consume(
        function (Message $message) use ($app) {
            dump($message);
            if ($message->getHeader('message_type') === 'conference_management.conference_created') {
                $eventData = json_decode($message->content);
                $projection = new \stdClass();
                $projection->id = $eventData->id;
                $projection->name = $eventData->name;

                // store the initial projection
                $app->redis()->hset(
                    'conferences',
                    $projection->id,
                    json_encode($projection)
                );
//
//                // load projection by ID
//                $projection = json_decode(
//                    $app->redis()->hget('conferences', $eventData->id)
//                );
            }
        }
    );
});
