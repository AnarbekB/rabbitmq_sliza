<?php

namespace App\Consumer;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use ErrorException;

abstract class AbstractConsumer
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

    /**
     * @var string
     */
    protected $consumerTag;

    public function __construct(AMQPStreamConnection $connection, string $queue, string $exchange, string $consumerTag)
    {
        $this->connection = $connection;
        $this->channel = $connection->channel();
        $this->queue = $queue;
        $this->exchange = $exchange;
        $this->consumerTag = $consumerTag;
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @throws ErrorException
     */
    public function listen()
    {
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_bind($this->queue, $this->exchange);
        $this->channel->basic_consume($this->queue, $this->consumerTag, false, false, false, false, [$this, 'execute']);

        while ($this->isConsuming()) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }

    public function isConsuming()
    {
        return count($this->channel->callbacks);
    }

    /**
     * @param AMQPMessage $message
     */
    public function execute(AMQPMessage $message)
    {
        //implement it in child consumer
    }
}
