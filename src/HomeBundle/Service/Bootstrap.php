<?php

namespace HomeBundle\Service;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use CommonBundle\Handler\MqttHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Application\BoilerApplication;
use HomeBundle\Application\InhabitantsMonitorApplication;
use HomeBundle\Entity\BeamIntersectionSensor;
use HomeBundle\Entity\BoilerUnit;
use HomeBundle\Entity\SwitchUnit;
use HomeBundle\Entity\TemperatureHumidityUnit;
use HomeBundle\Handler\BeamIntersectionSensorHandler;
use HomeBundle\Handler\BoilerHandler;
use HomeBundle\Handler\ModuleLogHandler;
use HomeBundle\Handler\PingHandler;
use HomeBundle\Handler\ModuleRegisterHandler;
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
     * @var DataStorage
     */
    private $storage;

    /**
     * @var InhabitantsMonitorApplication
     */
    private $inhabitantsMonitorApplication;

    /**
     * Bootstrap constructor.
     *
     * @param ContainerInterface            $container
     * @param DataStorage                   $storage
     * @param InhabitantsMonitorApplication $inhabitantsMonitorApplication
     */
    public function __construct(ContainerInterface $container, DataStorage $storage, InhabitantsMonitorApplication $inhabitantsMonitorApplication)
    {
        $this->manager = $container->get(EntityManager::class);
        $this->handler = $container->get(MqttHandler::class);
        $this->client = $container->get(ReactMqttClient::class);
        $this->container = $container;
        $this->storage = $storage;
        $this->inhabitantsMonitorApplication = $inhabitantsMonitorApplication;
    }

    public function boot()
    {
        $modules = $this->manager->getRepository('HomeBundle:Module')->findAll();
        $units = $this->manager->getRepository('HomeBundle:Unit')->findAll();

        foreach ($modules as $module) {
            $this->handler->registerHandler(new PingHandler($module));
            $this->handler->registerHandler(new ModuleRegisterHandler($module, $this->manager, $this->storage));
            $this->handler->registerHandler(new ModuleLogHandler($module, $this->storage));
        }

        foreach ($units as $unit) {

            switch (true) {
                case ($unit instanceof BeamIntersectionSensor): {

                    $this->handler->registerHandler(new BeamIntersectionSensorHandler($unit, $this->manager, $this->client, $this->storage));

                } break;

                case ($unit instanceof BoilerUnit): {

                    $this->handler->registerHandler(new BoilerHandler($unit, $this->manager, $this->storage));
                    $application = new BoilerApplication($this->container, $unit);
                    $application->start();

                } break;

                case ($unit instanceof SwitchUnit): {

                    $this->handler->registerHandler(new SwitchHandler($unit, $this->manager, $this->storage));

                } break;

                case ($unit instanceof TemperatureHumidityUnit): {


                    $this->handler->registerHandler(new TemperatureHandler($unit, $this->manager, $this->storage));

                } break;

                default:
                    continue;
            }

        }

        $this->inhabitantsMonitorApplication->start();
    }
}