<?php
declare(strict_types=1);

namespace Shared\RabbitMQ;

use Bunny\Client;
use Bunny\Channel;

trait NeedsChannel
{
    private static $channel;

    private static function exchangeName(): string
    {
        return 'messages';
    }

    /**
     * @return Channel
     */
    protected static function channel() : Channel
    {
        if (self::$channel === null) {
            $connection = [
                'host' => 'rabbitmq',
                'vhost' => '/',
                'user' => 'user',
                'password' => 'password'
            ];

            $client = new Client($connection);
            $client->connect();

            self::$channel = $client->channel();

            self::$channel->exchangeDeclare(
                static::exchangeName(),
                'topic',
                false,  // not passive: check if exchange declarations are compatible
                false, // not durable: exchange won't be recreated upon server restart
                false // auto-delete: when no queues are bound to this exchanges, it will not be auto-deleted
            );
        }

        return self::$channel;
    }
}
