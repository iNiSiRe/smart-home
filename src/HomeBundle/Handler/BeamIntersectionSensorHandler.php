<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\Unit;

class BeamIntersectionSensorHandler extends AbstractHandler
{
    /**
     * @var Unit
     */
    private $unit;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * BeamIntersectionSensorHandler constructor.
     *
     * @param Unit        $unit
     * @param EntityManager $manager
     */
    function __construct(Unit $unit, EntityManager $manager)
    {
        $this->unit = $unit;
        $this->manager = $manager;
    }

    /**
     * @return string
     */
    function getTopic()
    {
        return 'units/' . $this->unit->getId();
    }

    /**
     * @param Message $message
     *
     * @return void
     */
    function onMessage(Message $message)
    {

    }
}