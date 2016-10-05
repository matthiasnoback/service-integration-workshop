<?php

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/** @var AMQPChannel $channel */
$channel = require __DIR__ . '/bootstrap.php';

$callback = function (AMQPMessage $amqpMessage) {
    echo " [x] Received ", $amqpMessage->body, "\n";
    sleep(substr_count($amqpMessage->body, '.'));
    echo " [x] Done", "\n";

    /** @var AMQPChannel $channel */
    $channel = $amqpMessage->delivery_info['channel'];
    $channel->basic_ack($amqpMessage->delivery_info['delivery_tag']);
};

// prefetch only one message
$channel->basic_qos(null, 1, null);

// consume a message by invoking $callback; wait for ACK
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}
