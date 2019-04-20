<?php

namespace App\Producer;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var string
     */
    protected $queue;

    /**
     * @var string
     */
    protected $exchange;

    public function __construct(AMQPStreamConnection $connection, string $queue, string $exchange)
    {
        $this->connection = $connection;
        $this->channel = $connection->channel();
        $this->queue = $queue;
        $this->exchange = $exchange;
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function publish(string $message)
    {
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_bind($this->queue, $this->exchange);

        $message = new AMQPMessage($message, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $this->channel->basic_publish($message, $this->exchange);

        $this->channel->close();
        $this->connection->close();
    }
}
