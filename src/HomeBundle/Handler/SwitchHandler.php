<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\SwitchUnit;
use HomeBundle\Entity\Unit;
use HomeBundle\Service\DataStorage;
use inisire\ReactBundle\Threaded\Pool;
use inisire\ReactBundle\Threaded\ServiceMethodCall;

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
     * @var DataStorage
     */
    private $storage;

    /**
     * SwitchHandler constructor.
     *
     * @param SwitchUnit    $unit
     * @param EntityManager $manager
     * @param DataStorage   $storage
     */
    public function __construct(SwitchUnit $unit, EntityManager $manager, DataStorage $storage)
    {
        $this->unit = $unit;
        $this->manager = $manager;
        $this->storage = $storage;
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
     * @throws \Exception
     */
    function onMessage(Message $message)
    {
        $data = json_decode($message->getPayload(), true);

        $this->manager->refresh($this->unit);

        $enabled = (bool) ($data['enabled'] ?? false);
        $this->unit->setEnabled($enabled);
        $this->manager->flush($this->unit);

        $data = [
            'unit'    => $this->unit->getId(),
            'type'    => 'switch',
            'enabled' => $enabled
        ];

        $this->storage->store('log', $data);
    }
}