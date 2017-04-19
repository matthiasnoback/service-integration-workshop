<?php
declare(strict_types=1);

use Predis\Client;
use Ramsey\Uuid\Uuid;

require __DIR__ . '/../../vendor/autoload.php';

$redis = new Client([
    'host' => 'redis'
]);

$eventData = new \stdClass();
$eventData->id = (string)Uuid::uuid4();
$eventData->name = 'Conference ' . random_int(1, 10000);

/*
 * begin sample
 */

$projection = new \stdClass();
$projection->id = $eventData->id;
$projection->name = $eventData->name;

// store the initial projection
$redis->hset(
    'conferences',
    $projection->id,
    json_encode($projection)
);

// load projection by ID
$projection = json_decode(
    $redis->hget('conferences', $eventData->id)
);

// load all projections
$projections = array_map('json_decode', $redis->hgetall('conferences'));

/*
 * end sample
 */

dump($projection);

dump($projections);
