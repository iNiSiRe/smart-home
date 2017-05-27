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

        $this->logger->info('Add listener to input');

        $event = sprintf('module.%s', $module->getId());

        $listener = function ($message) use ($connection, $event) {

            $client = $this->clientStorage->getByConnection($connection);

            if (!$client) {
                $this->logger->info("Client disconnected, remove control listener");
                $this->emitter->removeAllListeners($event);
                return;
            }

            $this->logger->info('Perform listener');

            $connection->send(json_encode($message));
        };

        $this->emitter->on($event, $listener);

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