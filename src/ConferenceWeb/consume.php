<?php
declare(strict_types=1);

use Bunny\Message;
use ConferenceWeb\Conference;
use ConferenceWeb\Application;
use NaiveSerializer\Serializer;
use Shared\RabbitMQ\Queue;
use function Common\Resilience\retry;

require __DIR__ . '/../../vendor/autoload.php';

$app = new Application();

retry(3, 1000, function () use ($app) {
    Queue::consume(
        function (Message $message) use ($app) {
            if ($message->getHeader('message_type') === 'conference_management.conference_created') {
                $conferenceData = json_decode($message->content);
                $projection = new Conference();
                $projection->id = $conferenceData->id;
                $projection->name = $conferenceData->name;
                // store the initial projection
                $app->redis()->hset(
                    'conferences',
                    $projection->id,
                    Serializer::serialize($projection)
                );
            }
        },
        'conference_web'
    );
});
