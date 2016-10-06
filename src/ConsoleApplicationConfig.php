<?php

use Webmozart\Console\Api\Args\Format\Argument;
use Webmozart\Console\Api\Args\Format\Option;
use Webmozart\Console\Api\Formatter\Style;
use Webmozart\Console\Config\DefaultApplicationConfig;

final class ConsoleApplicationConfig extends DefaultApplicationConfig
{
    /**
     * @var ServiceContainer
     */
    private $serviceContainer;

    public function __construct(ServiceContainer $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;

        parent::__construct('rabbitmq');
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->beginCommand('publish')
            ->setHandler([$this->serviceContainer, 'publishMessageCommandHandler'])
            ->addArgument('data', Argument::REQUIRED, 'The data to publish')
            ->addOption('exchange', 'e', Option::REQUIRED_VALUE | Option::STRING,
                'The exchange to publish the message to', 'messages')
            ->addOption('routing-key', 'r', Option::REQUIRED_VALUE | Option::STRING,
                'The routing key to use when publishing the message', 'messages.demo')
            ->end();

        $this
            ->beginCommand('consume')
            ->setHandler([$this->serviceContainer, 'consumeMessageCommandHandler'])
            ->addOption('queue', 'u', Option::REQUIRED_VALUE | Option::STRING,
                'The queue to consume messages from (a random name by default)', '')
            ->addOption('exchange', 'e', Option::REQUIRED_VALUE | Option::STRING,
                'The exchange to bind the queue to', 'messages')
            ->addOption('binding', 'b', Option::REQUIRED_VALUE | Option::STRING,
                'The binding to use for the queue', '#')
            ->end();

        $this->addStyles(
            [
                Style::tag('success')->fgGreen()
            ]
        );
    }
}
