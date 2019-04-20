<?php

include(__DIR__ . '/config/config.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

$count = (int)$argv[1];

if ($argv[1] == 'quit') {
    send($argv[1]);
    exit;
}

if ($count <= 0) {
    echo 'Error, count need int' . PHP_EOL;
    exit;
} else {
    $messageBody = json_encode(['count' => $argv[1]]);
    send($messageBody);
    exit;
}

function send(string $messageBody)
{
    $connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
    $channel = $connection->channel();

    $channel->queue_declare(QUEUE, false, true, false, false);
    $channel->exchange_declare(EXCHANGE, AMQPExchangeType::DIRECT, false, true, false);
    $channel->queue_bind(QUEUE, EXCHANGE);

    $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
    $channel->basic_publish($message, EXCHANGE);

    $channel->close();
    $connection->close();
}
