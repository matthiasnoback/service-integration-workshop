<?php
declare(strict_types=1);

namespace Shared\RabbitMQ;

use NaiveSerializer\Serializer;
use function Common\CommandLine\line;
use function Common\CommandLine\make_blue;
use function Common\CommandLine\stdout;
use function Common\Resilience\retry;

final class Exchange
{
    use NeedsChannel;

    public static function publishEvent(string $type, $event): void
    {
        retry(15, 1000, function () use ($type, $event) {
            self::publish($type, $event, 'events');
        });
    }

    public static function publishCommand(string $type, $command): void
    {
        retry(15, 1000, function () use ($type, $command) {
            self::publish($type, $command, 'commands');
        });
    }

    private static function publish(string $type, $data, string $exchange): void
    {
        $serializedData = Serializer::serialize($data);

        self::channel()->publish(
            $serializedData,
            [
                'delivery_mode' => 2, // persistent message
                'content_type' => 'application/json',
                'message_type' => $type
            ],
            $exchange,
            $type
        );

        stdout(line(make_blue('Published'), $serializedData));
    }
}
