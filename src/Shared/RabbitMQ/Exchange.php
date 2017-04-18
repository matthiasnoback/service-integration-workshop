<?php
declare(strict_types=1);

namespace Shared\RabbitMQ;

use function Common\CommandLine\make_green;
use NaiveSerializer\Serializer;
use function Common\CommandLine\line;
use function Common\CommandLine\stdout;
use function Common\Resilience\retry;
use Shared\StringUtil;

final class Exchange
{
    use NeedsChannel;

    public static function publish(string $type, $data): void
    {
        retry(15, 1000, function () use ($type, $data) {
            $serializedData = Serializer::serialize($data);

            self::channel()->publish(
                $serializedData,
                [
                    'delivery_mode' => 2, // persistent message
                    'content_type' => 'application/json',
                    'message_type' => $type
                ],
                static::exchangeName(),
                $type
            );

            stdout(
                line(make_green('Published message:')),
                $serializedData
            );
        });
    }
}
