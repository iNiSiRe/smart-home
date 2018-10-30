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
     * @var Pool
     */
    private $pool;

    /**
     * SwitchHandler constructor.
     *
     * @param SwitchUnit    $unit
     * @param EntityManager $manager
     * @param Pool          $pool
     */
    public function __construct(SwitchUnit $unit, EntityManager $manager, Pool $pool)
    {
        $this->unit = $unit;
        $this->manager = $manager;
        $this->pool = $pool;
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

        $enabled = (bool) ($data['enabled'] ?? false);
        $this->unit->setEnabled($enabled);
        $this->manager->flush($this->unit);

        $data = [
            'unit'    => $this->unit->getId(),
            'type'    => 'switch',
            'enabled' => $enabled
        ];

        $this->pool->submit(new ServiceMethodCall(DataStorage::class, 'store', ['log', $data]));
    }
}