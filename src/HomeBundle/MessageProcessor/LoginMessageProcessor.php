<?php

namespace HomeBundle\MessageProcessor;

use HomeBundle\Actions;
use HomeBundle\Entity\Unit;
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
        $id = $message['id'];

        $module = $this->entityManager->getRepository('HomeBundle:Module')->find($id);

        if (!$module) {
            return;
        }

        $this->clientStorage->add(new Client($connection, 0, $id));

        foreach ($module->getUnits() as $entityUnit) {

            if ($entityUnit->getType() !== Unit::TYPE_CONTROLLER) {
                continue;
            }

            $this->logger->info('Add listener to input');

            $event = sprintf('unit.%s.%s.command.control', $module->getId(), $entityUnit->getName());

            $listener = function ($id, $unit, $value) use ($connection, $event, $entityUnit) {

                $client = $this->clientStorage->getByConnection($connection);

                if (!$client) {
                    $this->logger->info("Client disconnected, remove control listener");
                    $this->emitter->removeAllListeners($event);
                    return;
                }

                $this->logger->info('Perform listener');

                $entityUnit = $this->entityManager->getRepository('HomeBundle:Unit')->find($entityUnit->getId());
                $entityUnit->setValue($value);
                $this->entityManager->persist($entityUnit);
                $this->entityManager->flush();

                $connection->send(json_encode([
                    'action' => Actions::ACTION_CONTROL,
                    'resource' => 'input',
                    'unit' => $unit,
                    'value' => $value
                ]));
            };

            $this->emitter->on($event, $listener);
        }

        $this->logger->info("Module #{$id} login success");
    }

    /**
     * @return int
     */
    public function getMessageType()
    {
        return Actions::ACTION_LOGIN;
    }
}