<?php

namespace HomeBundle\MessageProcessor;

use HomeBundle\Actions;
use Ratchet\ConnectionInterface;

class SignalMessageProcessor extends AbstractMessageProcessor
{
    /**
     * @param ConnectionInterface $connection
     * @param $message
     */
    public function process(ConnectionInterface $connection, $message)
    {
        $resource = $message['resource'];
        
        switch ($resource) {

            case 'input':
                $id = $message['id'];
                $unit = $message['name'];
                $value = $message['value'];
                $event = sprintf('unit.%s.%s.command.control', $id, $unit);

                $this->logger->info(sprintf('emit input event %s, %s, %s', $id, $unit, $value));

                $this->emitter->emit($event, [$id, $unit, $value]);
                break;

            case 'update':
                $client = $this->clientStorage->get($message['id']);

                if (!$client) {
                    $this->logger->error('No connection with given uid');
                    break;
                }

                $client->getConnection()->send(json_encode([
                    'action' => Actions::ACTION_UPDATE
                ]));

                break;
        }
    }

    /**
     * @return int
     */
    public function getMessageType()
    {
        return Actions::ACTION_SIGNAL;
    }
}