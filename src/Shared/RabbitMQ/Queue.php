<?php
declare(strict_types=1);

namespace Shared\RabbitMQ;

use Bunny\Channel;
use Bunny\Message;
use function Common\CommandLine\line;
use function Common\CommandLine\make_green;
use function Common\CommandLine\stderr;
use function Common\CommandLine\stdout;
use function Common\CommandLine\make_red;

final class Queue
{
    use NeedsChannel;

    public static function consume(callable $userCallback): void
    {
        $channel = self::channel();

        $channel->queueDeclare(
            static::queueName(),
            false, // not passive; check if queue declarations are compatible
            false, // not durable; queue won't be recreated upon server restart
            false, // not exclusive; can be shared between connections
            true // auto-delete: when all consumers have finished using it, the queue gets deleted
        );
        // set up a default queue, receiving all messages from the exchange
        $channel->queueBind(self::queueName(), self::exchangeName(), '#');

        $callback = function (Message $message, Channel $channel) use ($userCallback) {
            stdout(
                line(make_green('Received message:')),
                line($message->content)
            );

            try {
                $userCallback($message, $channel);
            } catch (\Throwable $fault) {
                /*
                 * You'd need to log the exception, and send the message to something like a "dead letter" exchange.
                 * For now, we just print the exception
                 */
                stderr(line(make_red('Error'), (string)$fault));
            } finally {
                // we acknowledge anyway, to prevent the queue from flooding
                $channel->ack($message);
                stdout(line(make_green('Message ACK')));
            }
        };

        // prefetch only one message at a time
        $channel->qos(0, 1, false);

        // consume a message by invoking $callback
        $channel->run(
            $callback,
            self::queueName(),
            '', // consumer tag
            false, // local: also accept messages from same connection
            false, // ack: wait for ack
            false, // not exclusive: can be shared between connections
            false // wait: wait for a reply from the server
        );
    }

    private static function queueName()
    {
        return 'messages';
    }
}
