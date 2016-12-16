<?php

namespace Shared\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

trait Channel
{
    private static $channel;

    /**
     * @return AMQPChannel
     */
    protected static function channel() : AMQPChannel
    {
        if (self::$channel === null) {
            $connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'password', '/');
            self::$channel = $connection->channel();

            self::$channel->exchange_declare(
                'events',
                'topic',
                false,  // not passive: check if exchange declarations are compatible
                false, // not durable: exchange won't be recreated upon server restart
                false // auto-delete: when no queues are bound to this exchanges, it will not be auto-deleted
            );

            self::$channel->exchange_declare(
                'commands',
                'topic',
                false,  // not passive: check if exchange declarations are compatible
                false, // not durable: exchange won't be recreated upon server restart
                false // auto-delete: when no queues are bound to this exchanges, it will not be auto-deleted
            );
        }

        return self::$channel;
    }
}
