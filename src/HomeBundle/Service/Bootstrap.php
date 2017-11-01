<?php

namespace HomeBundle\Service;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use CommonBundle\Handler\MqttHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Handler\BeamIntersectionSensorHandler;
use HomeBundle\Handler\SwitchHandler;

class Bootstrap
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var MqttHandler
     */
    private $handler;

    /**
     * @var ReactMqttClient
     */
    private $client;

    /**
     * Bootstrap constructor.
     *
     * @param EntityManager   $manager
     * @param MqttHandler     $handler
     * @param ReactMqttClient $client
     */
    public function __construct(EntityManager $manager, MqttHandler $handler, ReactMqttClient $client)
    {
        $this->manager = $manager;
        $this->handler = $handler;
        $this->client = $client;
    }

    public function boot()
    {
        $units = $this->manager->getRepository('HomeBundle:Unit')->findAll();

        foreach ($units as $unit) {

            switch ($unit->getClass()) {
                case 'BeamIntersectionSensor': {

                    $this->handler->registerHandler(new BeamIntersectionSensorHandler($unit, $this->manager, $this->client));

                } break;

                case 'Switch': {

                    $this->handler->registerHandler(new SwitchHandler($unit, $this->manager));

                } break;

                default:
                    continue;
            }

        }
    }
}