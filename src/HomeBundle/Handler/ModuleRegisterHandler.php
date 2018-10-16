<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\Module;

class ModuleRegisterHandler extends AbstractHandler
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var Module
     */
    private $module;

    /**
     * RegisterOnServerHandler constructor.
     *
     * @param Module        $module
     * @param EntityManager $manager
     */
    public function __construct(Module $module, EntityManager $manager)
    {
        $this->manager = $manager;
        $this->module = $module;
    }

    /**
     * @return string
     */
    function getTopic()
    {
        return 'modules/' . $this->module->getId() . '/register';
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

        $ip = $data['ip'] ?? null;
        $deviceId = $data['device_id'] ?? null;

        $this->module
            ->setIp($ip)
            ->setCode($deviceId);

        $this->manager->flush($this->module);
    }
}