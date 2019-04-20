<?php

include(__DIR__ . '/../config/config.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
$channel = $connection->channel();
$channel->queue_declare(QUEUE, false, true, false, false);
$channel->exchange_declare(EXCHANGE, AMQPExchangeType::DIRECT, false, true, false);
$channel->queue_bind(QUEUE, EXCHANGE);
$channel->basic_consume(QUEUE, CONSUMER_TAG, false, false, false, false, 'process_message');

/**
 * @param \PhpAmqpLib\Channel\AMQPChannel $channel
 * @param \PhpAmqpLib\Connection\AbstractConnection $connection
 */
function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown', $channel, $connection);

while (isConsuming($channel)) {
    try {
        $channel->wait();
    } catch (Throwable $throwable) {
        //
    }
}

function isConsuming($channel)
{
    return count($channel->callbacks);
}

/**
 * @param \PhpAmqpLib\Message\AMQPMessage $message
 */
function process_message($message)
{
    echo "\n--------\n";
    echo $message->body;
    echo "\n--------\n";

    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

    // Send a message with the string "quit" to cancel the consumer.
    if ($message->body === 'quit') {
        $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
    }

    $params = json_decode($message->body, true);

    $result = '';
    foreach (range(1, $params['count']) as $item) {
        $result .= fibonacci($item) . ', ';
    }

    echo "\nresult:\n";
    echo $result;
    echo "\n--------\n";
}

function fibonacci(int $n)
{
    if ($n < 3) {
        return 1;
    }
    else {
        return fibonacci($n-1) + fibonacci($n-2);
    }
}
