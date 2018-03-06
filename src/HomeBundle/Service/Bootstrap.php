<?php

namespace HomeBundle\Service;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use CommonBundle\Handler\MqttHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Application\BoilerApplication;
use HomeBundle\Entity\BeamIntersectionSensor;
use HomeBundle\Entity\BoilerUnit;
use HomeBundle\Entity\SwitchUnit;
use HomeBundle\Entity\TemperatureHumidityUnit;
use HomeBundle\Handler\BeamIntersectionSensorHandler;
use HomeBundle\Handler\BoilerHandler;
use HomeBundle\Handler\SwitchHandler;
use HomeBundle\Handler\TemperatureHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @var ContainerInterface
     */
    private $container;

    /**
     * Bootstrap constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->manager = $container->get(EntityManager::class);
        $this->handler = $container->get(MqttHandler::class);
        $this->client = $container->get(ReactMqttClient::class);
        $this->container = $container;
    }

    public function boot()
    {
        $units = $this->manager->getRepository('HomeBundle:Unit')->findAll();

        foreach ($units as $unit) {

            switch (true) {
                case ($unit instanceof BeamIntersectionSensor): {

                    $this->handler->registerHandler(new BeamIntersectionSensorHandler($unit, $this->manager, $this->client));

                } break;

                case ($unit instanceof BoilerUnit): {

                    $this->handler->registerHandler(new BoilerHandler($unit));
                    $application = new BoilerApplication($this->container, $unit);
                    $application->start();

                } break;

                case ($unit instanceof SwitchUnit): {

                    $this->handler->registerHandler(new SwitchHandler($unit, $this->manager));

                } break;

                case ($unit instanceof TemperatureHumidityUnit): {

                    $this->handler->registerHandler(new TemperatureHandler($unit, $this->manager));

                } break;

                default:
                    continue;
            }

        }
    }
}