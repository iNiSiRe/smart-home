<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\Module;

class PingHandler extends AbstractHandler
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @param Module        $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    function getTopic()
    {
        return 'modules/'. $this->module->getId() . '/ping';
    }

    /**
     * @param Message $message
     *
     * @return void
     */
    function onMessage(Message $message)
    {
        $this->module->setLastPing(new \DateTime());
    }
}