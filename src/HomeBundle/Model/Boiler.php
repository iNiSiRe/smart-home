<?php

namespace HomeBundle\Model;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use BinSoul\Net\Mqtt\DefaultMessage;
use HomeBundle\Entity\BoilerUnit;

class Boiler
{
    /**
     * @var BoilerUnit
     */
    private $boiler;

    /**
     * @var ReactMqttClient
     */
    private $client;

    /**
     * Boiler constructor.
     *
     * @param BoilerUnit      $boiler
     * @param ReactMqttClient $client
     */
    public function __construct(BoilerUnit $boiler, ReactMqttClient $client)
    {
        $this->boiler = $boiler;
        $this->client = $client;
    }

    public function enable()
    {
        if ($this->boiler->isEnabled()) {
            return;
        }

        $this->boiler->setEnabled(true);

        $this->client->publish(new DefaultMessage(
            'units/' . $this->boiler->getId(),
            json_encode(['enabled' => true]),
            2
        ));
    }

    public function disable()
    {
        if (!$this->boiler->isEnabled()) {
            return;
        }

        $this->boiler->setEnabled(false);

        $this->client->publish(new DefaultMessage(
            'units/' . $this->boiler->getId(),
            json_encode(['enabled' => false]),
            2
        ));
    }
}