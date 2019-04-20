<?php

include(__DIR__ . '/config/config.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Producer\Producer;

$count = (int)$argv[1];

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
$producer = new Producer($connection, QUEUE, EXCHANGE);

if ($argv[1] == 'quit') {
    $producer->publish($argv[1]);
    exit;
}

if ($count <= 0) {
    echo 'Error, count need int' . PHP_EOL;
    exit;
} else {
    $messageBody = json_encode(['count' => $argv[1]]);
    $producer->publish($messageBody);
    exit;
}
