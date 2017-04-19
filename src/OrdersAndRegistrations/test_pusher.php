<?php
declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

$options = array(
    'cluster' => 'eu',
    'encrypted' => true,
    'debug' => true
);

$pusher = new Pusher(
    getenv('PUSHER_KEY'),
    getenv('PUSHER_SECRET'),
    getenv('PUSHER_APP_ID'),
    $options
);

$data['message'] = 'hello world';
$result = $pusher->trigger('my-channel', 'my-event', $data);
if (!is_bool($result)) {
    dump($result);
}
