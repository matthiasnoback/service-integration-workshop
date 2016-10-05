<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;

require __DIR__ . '/vendor/autoload.php';

$conn = new AMQPStreamConnection('localhost', 5672, 'user', 'password', '/');

$channel = $conn->channel();

$channel->exchange_declare('tasks', 'direct', false, true, false);
$channel->queue_declare('task_queue', false, true, false, false);
$channel->queue_bind('task_queue', 'tasks');

return $channel;
