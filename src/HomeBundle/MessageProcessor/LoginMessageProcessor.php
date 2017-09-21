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
        $module = $this->entityManager->getRepository('HomeBundle:Module')->find($message['module']);

        if (!$module) {
            return;
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