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
use HomeBundle\Service\DataStorage;

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
     * @var DataStorage
     */
    private $storage;

    /**
     * BeamIntersectionSensorHandler constructor.
     *
     * @param BeamIntersectionSensor $unit
     * @param EntityManager          $manager
     * @param ReactMqttClient        $client
     */
    function __construct(BeamIntersectionSensor $unit, EntityManager $manager, ReactMqttClient $client, DataStorage $storage)
    {
        $this->unit = $unit;
        $this->manager = $manager;
        $this->client = $client;
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
     */
    function onMessage(Message $message)
    {
        $data = json_decode($message->getPayload(), true);
        $direction = $data['direction'] ?? null;

        if ($direction === null) {
            return;
        }

        if ($direction == 'in') {
            $this->unit->getRoomTo()->incrementInhabitants(+1);
            $this->unit->getRoomFrom()->incrementInhabitants(-1);
        } else {
            $this->unit->getRoomTo()->incrementInhabitants(-1);
            $this->unit->getRoomFrom()->incrementInhabitants(+1);
        }

        if ($this->unit->getRoomTo()->getInhabitants() > 0 && $this->unit->getLight()->isEnabled() == false) {
            $topic = 'units/' . $this->unit->getLight()->getId();
            $payload = json_encode(['enabled' => true]);
            $this->client->publish(new DefaultMessage($topic, $payload, 2));
        } elseif ($this->unit->getRoomTo()->getInhabitants() <= 0 && $this->unit->getLight()->isEnabled() == true) {
            $topic = 'units/' . $this->unit->getLight()->getId();
            $payload = json_encode(['enabled' => false]);
            $this->client->publish(new DefaultMessage($topic, $payload, 2));
        }

        $this->manager->flush();

        $this->storage->store('log', [
            'unit' => $this->unit->getId(),
            'type' => 'intersection',
            'direction' => $direction
        ]);
    }
}