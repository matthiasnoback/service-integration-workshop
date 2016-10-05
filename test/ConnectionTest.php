<?php

namespace Test;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_connect()
    {
        $exchange = 'router';
        $queue = 'messages';
        $conn = new AMQPStreamConnection('localhost', 5672, 'user', 'password', '/');
        $ch = $conn->channel();
        $ch->queue_declare($queue, false, true, false, false);

        $ch->exchange_declare($exchange, 'direct', false, true, false);
        $ch->queue_bind($queue, $exchange);

        $msg = new AMQPMessage('Hallo', array('content_type' => 'text/plain', 'delivery_mode' => 2));
        $ch->basic_publish($msg, $exchange);
        $ch->close();
        $conn->close();
    }
}
