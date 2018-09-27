<?php

namespace HomeBundle\Client;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use BinSoul\Net\Mqtt\DefaultSubscription;
use BinSoul\Net\Mqtt\IdentifierGenerator;
use BinSoul\Net\Mqtt\Message;
use BinSoul\Net\Mqtt\StreamParser;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectorInterface;

class MqttClient extends ReactMqttClient
{
    /**
     * @var int
     */
    private $id = 1;

    /**
     * @var array
     */
    private $waiting = [];

    /**
     * @param ConnectorInterface       $connector
     * @param LoopInterface            $loop
     * @param IdentifierGenerator|null $identifierGenerator
     * @param StreamParser|null        $parser
     */
    public function __construct(ConnectorInterface $connector, LoopInterface $loop, IdentifierGenerator $identifierGenerator = null, StreamParser $parser = null)
    {
        parent::__construct($connector, $loop, $identifierGenerator, $parser);

        $this->on('message', [$this, 'onMessage']);
        $this->subscribe(new DefaultSubscription("server.response"));
        $loop->addPeriodicTimer(1000, [$this, 'retry']);
    }

    /**
     * Perform retry for old messages
     */
    private function retry()
    {
        $current = time();

        foreach ($this->waiting as $id => $item) {

            /**
             * @var Message $message
             */
            list ($message, $time) = $item;

            $diff = $current - $time;

            if ($diff > 5) {
                syslog(LOG_WARNING, sprintf("Message can't be delivered '%s'", $message->getPayload()));
                unset($this->waiting[$id]);
            } else if ($diff > 1) {
                $this->publish($message);
            }
        }
    }

    /**
     * @param Message $message
     */
    private function onMessage(Message $message)
    {
        $payload = json_decode($message->getPayload(), true);

        $id = $payload['id'];
        $success = $payload['success'];

        if (!$success) {
            syslog(LOG_WARNING, $payload['error']);
        }

        if (isset($this->waiting[$id])) {
            // Response received
            unset($this->waiting[$id]);
        }
    }

    /**
     * @inheritDoc
     */
    public function publish(Message $message)
    {
        $payload = json_decode($message->getPayload(), true);

        $id = $this->id++;

        $payload['id'] = $id;
        $payload['from'] = 'server';

        $message = $message->withPayload(json_encode($payload));

        $this->waiting[$id] = [$message, time()];

        parent::publish($message);
    }
}