<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use BinSoul\Net\Mqtt\DefaultMessage;
use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Bridge\BeamIntersectionSensorBridge;
use HomeBundle\Entity\Unit;

class BeamIntersectionSensorHandler extends AbstractHandler
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
     * @var ReactMqttClient
     */
    private $client;

    /**
     * BeamIntersectionSensorHandler constructor.
     *
     * @param Unit            $unit
     * @param EntityManager   $manager
     * @param ReactMqttClient $client
     */
    function __construct(Unit $unit, EntityManager $manager, ReactMqttClient $client)
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
        $bridge = new BeamIntersectionSensorBridge($this->unit);

//        $roomFrom = $this->manager->getRepository('HomeBundle:Room')->findOneBy($bridge->getRoomFrom());
        $roomTo = $this->manager->getRepository('HomeBundle:Room')->findOneBy($bridge->getRoomTo());

        if ($roomTo === null) {
            return;
        }

        $data = json_decode($message->getPayload(), true);
        $direction = $data['direction'] ?? null;

        if ($direction === null) {
            return;
        }

        $inhabitantsTo = $roomTo->getVariable('inhabitants');

        if ($inhabitantsTo === null) {
            $inhabitantsTo = 0;
        }

        if ($direction == 'in') {
            $inhabitantsTo++;
        }

        $lightTo = $this->manager->getRepository('HomeBundle:Unit')->find($roomTo->getVariable('main_light'));

        if ($lightTo === null) {
            return;
        }

        if ($inhabitantsTo > 0 && $lightTo->getVariable('enabled') == false) {
            $topic = 'units/' . $lightTo->getId();
            $payload = json_encode(['enabled' => true]);
            $this->client->publish(new DefaultMessage($topic, $payload));
        } elseif ($inhabitantsTo <= 0 && $lightTo->getVariable('enabled') == true) {
            $inhabitantsTo = 0;
            $topic = 'units/' . $lightTo->getId();
            $payload = json_encode(['enabled' => false]);
            $this->client->publish(new DefaultMessage($topic, $payload));
        }

        $roomTo->setVariable('inhabitants', $inhabitantsTo);

        $this->manager->flush();
    }
}