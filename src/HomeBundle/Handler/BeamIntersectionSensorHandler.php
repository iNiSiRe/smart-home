<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use BinSoul\Net\Mqtt\DefaultMessage;
use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Bridge\BeamIntersectionSensorBridge;
use HomeBundle\Entity\BeamIntersectionSensor;
use HomeBundle\Entity\Unit;

class BeamIntersectionSensorHandler extends AbstractHandler
{
    /**
     * @var BeamIntersectionSensor
     */
    private $unit;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ReactMqttClient
     */
    private $client;

    /**
     * BeamIntersectionSensorHandler constructor.
     *
     * @param BeamIntersectionSensor $unit
     * @param EntityManager          $manager
     * @param ReactMqttClient        $client
     */
    function __construct(BeamIntersectionSensor $unit, EntityManager $manager, ReactMqttClient $client)
    {
        $this->unit = $unit;
        $this->manager = $manager;
        $this->client = $client;
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
        $direction = $data['direction'] ?? null;

        if ($direction === null) {
            return;
        }

        if ($direction == 'in') {
            $this->unit->getRoomTo()->incrementInhabitants(1);
        } else {
            $this->unit->getRoomTo()->incrementInhabitants(-1);
        }

        if ($this->unit->getRoomTo()->getInhabitants() > 0 && $this->unit->getLight()->isEnabled() == false) {
            $topic = 'units/' . $this->unit->getLight()->getId();
            $payload = json_encode(['enabled' => true]);
            $this->client->publish(new DefaultMessage($topic, $payload));
        } elseif ($this->unit->getRoomTo()->getInhabitants() <= 0 && $this->unit->getLight()->isEnabled() == true) {
            $topic = 'units/' . $this->unit->getLight()->getId();
            $payload = json_encode(['enabled' => false]);
            $this->client->publish(new DefaultMessage($topic, $payload));
        }

        $this->manager->flush();
    }
}