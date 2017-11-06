<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use HomeBundle\Entity\BoilerUnit;

class BoilerHandler extends AbstractHandler
{
    /**
     * @var BoilerUnit
     */
    private $unit;

    /**
     * BoilerHandler constructor.
     *
     * @param BoilerUnit $unit
     */
    public function __construct(BoilerUnit $unit)
    {
        $this->unit = $unit;
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