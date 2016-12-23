<?php

namespace Shared\RabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;
use function Shared\CommandLine\line;
use function Shared\CommandLine\make_magenta;
use function Shared\CommandLine\stderr;
use function Shared\CommandLine\stdout;
use function Shared\Resilience\retry;

final class Exchange
{
    use Channel;

    public static function publishEvent(array $event)
    {
        retry(30, 1000, function() use ($event) {
            self::publish($event, 'events');
        });
    }

    public static function publishCommand(array $command)
    {
        retry(30, 1000, function() use ($command) {
            self::publish($command, 'commands');
        });
    }

    private static function publish(array $data, string $exchange)
    {
        $encodedData = json_encode($data);
        $amqpMessage = new AMQPMessage($encodedData, [
            'delivery_mode' => 2 // persistent message
        ]);

        stdout(line('Published message ', make_magenta($encodedData)));
        
        self::channel()->basic_publish($amqpMessage, $exchange, $data['_type']);
    }
}
