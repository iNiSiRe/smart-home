<?php
/**
 * Created by PhpStorm.
 * User: user18
 * Date: 07.03.18
 * Time: 4:46
 */

namespace HomeBundle\Handler;


use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\Unit;

class RegisterOnServerHandler extends AbstractHandler
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
     * RegisterOnServerHandler constructor.
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
        return 'units/' . $this->unit->getId() . '/register';
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

        $this->unit
            ->setIp($ip)
            ->setDeviceId($deviceId);

        $this->unit->getModule()
            ->setIp($ip);

        $this->manager->flush($this->unit->getModule());
        $this->manager->flush($this->unit);
    }
}