<?php

require __DIR__ . '/../../vendor/autoload.php';

/*
 * Twitto - a web framework in a tweet - http://twitto.org/
 */
require __DIR__.'/c.php';
if (!is_callable($c = @$_GET['c'] ?: function() { echo 'Woah!'; }))
    throw new Exception('Error');
$c();
