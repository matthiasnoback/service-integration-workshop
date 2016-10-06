<?php

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\IO\IO;

final class ConsumeMessage
{
    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function handle(Args $args, IO $io)
    {
        $exchange = $args->getOption('exchange');
        $queue = $args->getOption('queue');
        $binding = $args->getOption('binding');

        $io->writeLine(sprintf('Declare exchange <c2>%s</c2>', $exchange));
        $this->channel->exchange_declare(
            $exchange,
            'topic',
            false,  // not passive: check if exchange declarations are compatible
            false, // not durable: exchange won't be recreated upon server restart
            false // no auto-delete: when no queues are bound to this exchanges, it will not be auto-deleted
        );

        $io->writeLine(sprintf('Declare queue <c2>%s</c2>', $queue));
        list($queue) = $this->channel->queue_declare(
            $queue,
            false, // not passive; check if queue declarations are compatible
            false, // not durable; queue won't be recreated upon server restart
            false, // not exclusive; can be shared between connections
            true // auto-delete: when all consumers have finished using it, the queue gets deleted
        );

        $io->writeLine(sprintf('Bind queue to exchange with binding <c2>%s</c2>', $binding));
        $this->channel->queue_bind($queue, $exchange, $binding);

        $callback = function (AMQPMessage $amqpMessage) use ($io) {
            $io->writeLine(sprintf('Received: "%s"', $amqpMessage->body));

            sleep(substr_count($amqpMessage->body, '.'));

            $io->writeLine('<success>Done</success>');

            /** @var AMQPChannel $channel */
            $channel = $amqpMessage->delivery_info['channel'];
            $channel->basic_ack($amqpMessage->delivery_info['delivery_tag']);
        };

        // prefetch only one message at a time
        $this->channel->basic_qos(null, 1, null);

        // consume a message by invoking $callback; wait for ACK
        $this->channel->basic_consume(
            $queue,
            '', // consumer tag
            false, // local: also accept messages from same connection
            false, // ack: wait for ack
            false, // not exclusive: can be shared between connections
            false, // wait: wait for a reply from the server
            $callback
        );

        while (count($this->channel->callbacks)) {
            $io->writeLine('Wait for the next message...');
            $this->channel->wait();
        }
    }
}
