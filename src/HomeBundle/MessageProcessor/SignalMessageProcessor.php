<?php

namespace HomeBundle\MessageProcessor;

use HomeBundle\Actions;
use HomeBundle\Entity\Unit;
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

            case 'sensor':

                $this->logger->info("Process sensor signal");

//                if ($message['class'] !== "transit") {
//                    return;
//                }

                $id = $this->clientStorage->getByConnection($connection)->getId();

                $module = $this->entityManager->getRepository('HomeBundle:Module')->find($id);

                if (!$module) {
                    return;
                }

                $lightUnit = null;

                $room = $module->getRoom();

                foreach ($room->getUnits() as $unit) {
                    if ($unit->getClass() == "light") {
                        $lightUnit = $unit;
                        break;
                    }
                }

                if (!$lightUnit) {
                    $this->logger->info("Light unit not found");
                    return;
                }

                $event = sprintf('unit.%s.%s.command.control', $lightUnit->getModule()->getId(), $lightUnit->getName());

                if ($message['value'] == 1) {
                    $this->logger->info("Increment inhabitants");
                    $room->incrementInhabitants();
                } elseif ($message['value'] == 2) {
                    $this->logger->info("Decrement inhabitants");
                    $room->incrementInhabitants(-1);
                }

                if ($room->getInhabitants() <= 0) {
                    $room->setInhabitants(0);
                    $this->logger->info("Disable light");
                    $this->emitter->emit($event, [$lightUnit->getModule()->getId(), $lightUnit->getModule()->getId(), 0]);
                } else {
                    $this->logger->info("Enable light");
                    $this->emitter->emit($event, [$lightUnit->getModule()->getId(), $lightUnit->getModule()->getId(), 1]);
                }

                $this->logger->info("Inhabitants: {$room->getInhabitants()}");

                $this->entityManager->flush($room);

                break;

            default: break;
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