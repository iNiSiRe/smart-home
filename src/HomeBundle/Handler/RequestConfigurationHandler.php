<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use BinSoul\Net\Mqtt\DefaultMessage;
use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\Unit;

class RequestConfigurationHandler extends AbstractHandler
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ReactMqttClient
     */
    private $client;

    /**
     * RequestConfigurationHandler constructor.
     *
     * @param EntityManager   $manager
     * @param ReactMqttClient $client
     */
    public function __construct(EntityManager $manager, ReactMqttClient $client)
    {
        $this->manager = $manager;
        $this->client = $client;
    }

    /**
     * @return string
     */
    function getTopic()
    {
        return 'request/configuration';
    }

    /**
     * @param Message $message
     *
     * @return void
     */
    function onMessage(Message $message)
    {
        $data = json_decode($message->getPayload(), true);

        $mac = $data['mac'] ?? null;

        if (!$mac) {
            return;
        }

        $module = $this->manager->getRepository('HomeBundle:Module')->findOneBy(['mac' => $mac]);

        if (!$module) {
            return;
        }

        $units = [];

        /**
         * @var Unit $unit
         */
        foreach ($module->getUnits() as $unit) {
            $units[] = [
                'id' => $unit->getId(),
                'class' => $unit->getClass(),
                'config' => $unit->getConfig(),
                'variables' => $unit->getVariables()
            ];
        }

        $message = json_encode($module->getConfig());
        $topic = 'modules/' . $module->getCode() . '/configuration';
        $this->client->publish(new DefaultMessage($topic, $message));

        $message = json_encode($units);
        $topic = 'modules/' . $module->getCode() . '/units';
        $this->client->publish(new DefaultMessage($topic, $message));
    }
}