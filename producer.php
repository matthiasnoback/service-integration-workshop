<?php

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/** @var AMQPChannel $channel */
$channel = require __DIR__ . '/bootstrap.php';

$data = $argv[1] ?? 'Hello, world!';

$msg = new AMQPMessage($data, [
    'delivery_mode' => 2 // persistent message
]);

$channel->basic_publish($msg, 'tasks');

echo " [x] Sent ", $data, "\n";
