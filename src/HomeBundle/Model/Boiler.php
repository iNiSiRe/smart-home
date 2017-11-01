<?php

namespace HomeBundle\Model;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
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

    }

    public function disable()
    {

    }
}