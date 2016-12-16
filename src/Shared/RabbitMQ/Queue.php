<?php

namespace Shared\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use function Shared\CommandLine\line;
use function Shared\CommandLine\make_green;
use function Shared\CommandLine\stderr;
use function Shared\CommandLine\stdout;
use function Shared\CommandLine\make_red;
use Shared\CommandLine;
use function Shared\Resilience\retry;

final class Queue
{
    use Channel;

    public static function consume($exchange, $queue, $binding, callable $userCallback)
    {
        $channel = self::channel();

        list($queue) = $channel->queue_declare(
            $queue,
            false, // not passive; check if queue declarations are compatible
            false, // not durable; queue won't be recreated upon server restart
            false, // not exclusive; can be shared between connections
            true // auto-delete: when all consumers have finished using it, the queue gets deleted
        );
        $channel->queue_bind($queue, $exchange, $binding);

        $callback = function (AMQPMessage $amqpMessage) use ($userCallback) {
            stdout(line(make_green('Received: '), $amqpMessage->body));

            try {
                $userCallback($amqpMessage);
            } catch (\Throwable $fault) {
                /*
                 * You'd need to log the exception, and send the message to something like a "dead letter" exchange.
                 * For now, we just print the exception
                 */
                stderr(line(make_red((string)$fault)));
            } finally {
                /** @var AMQPChannel $channel */
                $channel = $amqpMessage->delivery_info['channel'];
                // we acknowledge anyway, to prevent the queue from flooding
                $channel->basic_ack($amqpMessage->delivery_info['delivery_tag']);
                stdout(line(make_green('ACK')));
            }
        };

        // prefetch only one message at a time
        $channel->basic_qos(null, 1, null);

        // consume a message by invoking $callback
        $channel->basic_consume(
            $queue,
            '', // consumer tag
            false, // local: also accept messages from same connection
            false, // ack: wait for ack
            false, // not exclusive: can be shared between connections
            false, // wait: wait for a reply from the server
            $callback
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }
}
