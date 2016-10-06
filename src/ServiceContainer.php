<?php

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class ServiceContainer
{
    public function publishMessageCommandHandler() : PublishMessage
    {
        return new PublishMessage($this->rabbitMQChannel());
    }

    public function consumeMessageCommandHandler() : ConsumeMessage
    {
        return new ConsumeMessage($this->rabbitMQChannel());
    }

    private function rabbitMQChannel() : AMQPChannel
    {
        static $channel;
        return $channel ?? $channel = $this->rabbitMQConnection()->channel();
    }

    private function rabbitMQConnection() : AMQPStreamConnection
    {
        static $conn;
        return $conn ?? $conn = new AMQPStreamConnection('localhost', 5672, 'user', 'password', '/');
    }
}
