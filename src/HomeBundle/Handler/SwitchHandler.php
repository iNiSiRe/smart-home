<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\SwitchUnit;
use HomeBundle\Entity\Unit;

class SwitchHandler extends AbstractHandler
{
    /**
     * @var SwitchUnit
     */
    private $unit;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * SwitchHandler constructor.
     *
     * @param SwitchUnit    $unit
     * @param EntityManager $manager
     */
    public function __construct(SwitchUnit $unit, EntityManager $manager)
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
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    function onMessage(Message $message)
    {
        $data = json_decode($message->getPayload(), true);

        $this->manager->refresh($this->unit);

        $this->unit->setEnabled((bool) ($data['enabled'] ?? false));

        $this->manager->flush($this->unit);
    }
}