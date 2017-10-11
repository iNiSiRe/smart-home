<?php

namespace HomeBundle\MessageProcessor;

use HomeBundle\Actions;
use HomeBundle\Model\Client;
use Ratchet\ConnectionInterface;

class LoginMessageProcessor extends AbstractMessageProcessor
{
    /**
     * @param ConnectionInterface $connection
     * @param $message
     */
    public function process(ConnectionInterface $connection, $message)
    {
        $module = $this->entityManager
            ->getRepository('HomeBundle:Module')
            ->findOneBy(['mac' => $message['mac']]);

        if (!$module) {
            return;
        }

        $units = [];

        foreach ($module->getUnits() as $unit) {
            $units[] = [
                'id' => $unit->getId(),
                'type' => $unit->getType(),
                'class' => $unit->getClass()
            ];
        }

        $this->clientStorage->add(new Client($connection, 0, $module->getId()));

        $this->logger->info("Module #{$module->getId()} login success");
    }

    /**
     * @return int
     */
    public function getMessageType()
    {
        return Actions::ACTION_LOGIN;
    }
}