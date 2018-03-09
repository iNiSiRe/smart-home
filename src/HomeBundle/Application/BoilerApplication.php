<?php

namespace HomeBundle\Application;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use HomeBundle\Entity\BoilerUnit;
use HomeBundle\Model\Boiler;
use React\EventLoop\Timer\TimerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BoilerApplication
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var BoilerUnit
     */
    private $boilerUnit;

    /**
     * @var Boiler
     */
    private $boiler;

    /**
     * @var TimerInterface
     */
    private $timer;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;

    /**
     * BoilerApplication constructor.
     *
     * @param ContainerInterface $container
     * @param BoilerUnit         $boiler
     */
    public function __construct(ContainerInterface $container, BoilerUnit $boiler)
    {
        $this->container = $container;
        $this->boilerUnit = $boiler;
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
        $this->boiler = new Boiler($boiler, $this->container->get(ReactMqttClient::class));
    }

    public function isSatisfiedByTemperature()
    {
        $sensor = $this->boilerUnit->getSensors()[0];

        if ($sensor->getTemperature() > $this->boilerUnit->getTemperature()) {
            return true;
        }

        if (!$this->boilerUnit->isEnabled() && $sensor->getTemperature() <= ($this->boilerUnit->getTemperature() - 1)) {
            return false;
        }

        return true;
    }

    public function isSatisfiedBySchedule()
    {
        $time = new \DateTime('now', new \DateTimeZone('Europe/Kiev'));
        $hours = $time->format('H');

        if ($hours >= 0 && $hours <=8) {
            return true;
        } elseif ($hours > 8 && $hours < 0) {
            return false;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSatisfiedByInhabitantsCount()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isSatisfiedByManualMode()
    {
        return $this->boilerUnit->getManual()->isEnabled();
    }

    public function loop()
    {
       $this->manager->refresh($this->boilerUnit);


       $this->container->get('logger')->debug('BoilerApplication::loop -> begin');

       if ($this->boilerUnit->getManual()->isEnabled()) {
           $this->container->get('logger')->debug('BoilerApplication::loop -> manual mode');
           return;
       }

       if (!$this->isSatisfiedByTemperature()) {
           $this->container->get('logger')->debug('BoilerApplication::loop -> not satisfied by temp');
           $this->boiler->enable();
       } else {
           $this->container->get('logger')->debug('BoilerApplication::loop -> satisfied by temp');
           $this->boiler->disable();
       }

       $this->manager->flush($this->boilerUnit);
    }

    public function start()
    {
        $this->timer = $this->container->get('react.loop')->addPeriodicTimer(60, [$this, 'loop']);
    }
}