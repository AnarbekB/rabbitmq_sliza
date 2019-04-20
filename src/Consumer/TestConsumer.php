<?php

namespace App\Consumer;

use PhpAmqpLib\Message\AMQPMessage;

class TestConsumer extends AbstractConsumer
{
    public function execute(AMQPMessage $message)
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
            $result .= $this->fibonacci($item) . ', ';
        }

        echo "\nresult:\n";
        echo $result;
        echo "\n--------\n";
    }

    protected function fibonacci(int $n)
    {
        if ($n < 3) {
            return 1;
        }
        else {
            return $this->fibonacci($n-1) + $this->fibonacci($n-2);
        }
    }
}

