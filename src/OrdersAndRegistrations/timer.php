<?php

use React\EventLoop\Factory;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();

$loop->addPeriodicTimer(30, function() {
    error_log('30 seconds has passed');
});

$loop->run();
