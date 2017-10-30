<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\Unit;

class SwitchHandler extends AbstractHandler
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
     * SwitchHandler constructor.
     *
     * @param Unit          $unit
     * @param EntityManager $manager
     */
    public function __construct(Unit $unit, EntityManager $manager)
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
        $data = json_decode($message->getPayload(), true);

        $this->manager->refresh($this->unit);

        $variables = $this->unit->getVariables();
        $variables['enabled'] = $data['variables']['enabled'] ?? false;
        $this->unit->setVariables($variables);

        $this->manager->flush($this->unit);
    }
}