<?php

include(__DIR__ . '/../config/config.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Consumer\TestConsumer;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
$consumer = new TestConsumer($connection, QUEUE, EXCHANGE, CONSUMER_TAG);
try {
    $consumer->listen();
} catch (Throwable $throwable) {
    echo $throwable->getMessage() . PHP_EOL;
    echo $throwable->getFile() . ' ' . $throwable->getLine() . PHP_EOL;
    echo $throwable->getCode() . PHP_EOL;
}
