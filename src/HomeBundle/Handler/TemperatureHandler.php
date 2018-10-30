<?php
/**
 * Created by PhpStorm.
 * User: user18
 * Date: 06.03.18
 * Time: 6:04
 */

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\TemperatureHumidityUnit;
use HomeBundle\Service\DataStorage;
use inisire\ReactBundle\Threaded\Pool;
use inisire\ReactBundle\Threaded\ServiceMethodCall;

class TemperatureHandler extends AbstractHandler
{
    /**
     * @var TemperatureHumidityUnit
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
     * TemperatureHandler constructor.
     *
     * @param TemperatureHumidityUnit $unit
     * @param EntityManager           $manager
     * @param DataStorage             $storage
     */
    public function __construct(TemperatureHumidityUnit $unit, EntityManager $manager, DataStorage $storage)
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
        return 'units/' . $this->unit->getId() . '/indicators';
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

        if (isset($data['temperature'])) {
            $this->unit->setTemperature(round($data['temperature'], 2));
        }

        if (isset($data['humidity'])) {
            $this->unit->setHumidity(round($data['humidity'], 2));
        }

        $this->manager->flush($this->unit);

        $data = [
            'unit'        => $this->unit->getId(),
            'type'        => 'temperature',
            'temperature' => $this->unit->getTemperature(),
            'humidity'    => $this->unit->getHumidity()
        ];

        $this->storage->store('log', $data);
    }
}