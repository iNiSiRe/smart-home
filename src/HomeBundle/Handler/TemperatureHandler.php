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
     * TemperatureHandler constructor.
     *
     * @param TemperatureHumidityUnit $unit
     * @param EntityManager           $manager
     */
    public function __construct(TemperatureHumidityUnit $unit, EntityManager $manager)
    {
        $this->unit = $unit;
        $this->manager = $manager;
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
     */
    function onMessage(Message $message)
    {
        $data = json_decode($message->getPayload(), true);

        if (isset($data['temperature'])) {
            $this->manager->refresh($this->unit);
            $this->unit->setTemperature(round($data['temperature'], 2));
            $this->manager->flush($this->unit);
        }
    }
}