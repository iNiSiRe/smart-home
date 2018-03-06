<?php

namespace CommonBundle\Handler;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use BinSoul\Net\Mqtt\DefaultSubscription;
use BinSoul\Net\Mqtt\Message;
use CommonBundle\Bridge\WebSocketHttpBridge;

class MqttHandler
{
    /**
     * @var ReactMqttClient
     */
    private $client;

    /**
     * @var AbstractHandler[]
     */
    private $handlers;

    /**
     * @var string
     */
    private $mqttServer;

    /**
     * MqttHandler constructor.
     *
     * @param ReactMqttClient $client
     * @param string          $mqttServer
     */
    public function __construct(ReactMqttClient $client, $mqttServer)
    {
        $this->client = $client;

        $this->client->on('message', [$this, 'onMessage']);

        $client->on('error', function (\Exception $e) {
            echo sprintf("Error: %s\n", $e->getMessage());
        });

        $this->mqttServer = $mqttServer;
    }

    /**
     * @param Message $message
     *
     * @throws \Exception
     */
    public function onMessage(Message $message)
    {
        if (!isset($this->handlers[$message->getTopic()])) {
            throw new \Exception('Handler not exists');
        }

        $this->handlers[$message->getTopic()]->onMessage($message);
    }

    /**
     * @param AbstractHandler $handler
     */
    public function registerHandler(AbstractHandler $handler)
    {
        $this->handlers[$handler->getTopic()] = $handler;
    }

    /**
     * Start
     */
    public function start()
    {
        $this->client->connect($this->mqttServer)
            ->then(function () {
                foreach ($this->handlers as $handler) {
                    $this->client->subscribe(new DefaultSubscription($handler->getTopic()));
                }
            });
    }
}