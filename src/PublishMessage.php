<?php

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\IO\IO;

final class PublishMessage
{
    private $channel;

    public function __construct(AMQPChannel $channel)
    {
        $this->channel = $channel;
    }

    public function handle(Args $args, IO $io)
    {
        $data = $args->getArgument('data');
        $exchange = $args->getOption('exchange');
        $routingKey = $args->getOption('routing-key');

        $io->writeLine(sprintf('Declare exchange <c2>%s</c2>', $exchange));
        $this->channel->exchange_declare(
            $exchange,
            'topic',
            false,  // not passive: check if exchange declarations are compatible
            false, // not durable: exchange won't be recreated upon server restart
            false // auto-delete: when no queues are bound to this exchanges, it will not be auto-deleted
        );

        $amqpMessage = new AMQPMessage($data, [
            'delivery_mode' => 2 // persistent message
        ]);

        $this->channel->basic_publish($amqpMessage, $exchange, $routingKey);

        $io->writeLine(sprintf(
            'Published message: <c1>%s</c1> with routing key <c1>%s</c1>',
            $data,
            $routingKey
        ));
    }
}
