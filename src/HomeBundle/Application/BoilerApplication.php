<?php

namespace HomeBundle\Application;

use HomeBundle\Entity\BoilerUnit;
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
        $this->boiler = $boiler;
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
    }

    public function loop()
    {
       $this->manager->refresh($this->boiler);

       $time = new \DateTime('now', new \DateTimeZone('Europe/Kiev'));
       $hours = $time->format('H');

       if ($this->boiler->isEnabled() && ($hours >= 0 && $hours <=8)) {

       }
    }

    public function start()
    {
        $this->timer = $this->container->get('react.loop')->addPeriodicTimer(60, [$this, 'loop']);
    }
}