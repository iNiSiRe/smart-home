<?php

namespace HomeBundle\MessageProcessor;

use HomeBundle\Actions;
use HomeBundle\Entity\Unit;
use Ratchet\ConnectionInterface;

class SignalMessageProcessor extends AbstractMessageProcessor
{
    /**
     * @param int   $id
     * @param array $message
     */
    private function sentToModule($id, array $message)
    {
        $client = $this->clientStorage->get($id);

        if (!$client) {
            $this->logger->error(sprintf('Client #%s not found', $id));

            return;
        }

        $client->getConnection()->send(json_encode($message));
    }

    /**
     * @param ConnectionInterface $connection
     * @param                     $message
     */
    public function process(ConnectionInterface $connection, $message)
    {
        $resource = $message['resource'];

        switch ($resource) {

            case 'input':
                $id = $message['unit'];
                $value = $message['value'];

                $unit = $this->entityManager->getRepository('HomeBundle:Unit')->find($id);

                $message = [
                    "action" => Actions::ACTION_CONTROL,
                    "mode"   => $unit->getMode(),
                    "pin"    => $unit->getPin(),
                    "value"  => $value,
                ];

                $unit->setValue($value);
                $this->entityManager->flush();

                $this->sentToModule($unit->getModule()->getId(), $message);

                break;

            case 'update':
                $client = $this->clientStorage->get($message['id']);

                if (!$client) {
                    $this->logger->error('No connection with given uid');
                    break;
                }

                $client->getConnection()->send(json_encode([
                    'action' => Actions::ACTION_UPDATE,
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

                if ($message['value'] == 1) {
                    $this->logger->info("Increment inhabitants");
                    $room->incrementInhabitants();
                } elseif ($message['value'] == 2) {
                    $this->logger->info("Decrement inhabitants");
                    $room->incrementInhabitants(-1);
                }

                $message = [
                    "action" => Actions::ACTION_CONTROL,
                    "mode"   => $lightUnit->getMode(),
                    "pin"    => $lightUnit->getPin()
                ];

                if ($room->getInhabitants() <= 0) {
                    $room->setInhabitants(0);
                    $this->logger->info("Disable light");
                    $message['value'] = 0;
                } else {
                    $this->logger->info("Enable light");
                    $message['value'] = 1;
                }

                $this->sentToModule($lightUnit->getModule()->getId(), $message);

                $this->logger->info("Inhabitants: {$room->getInhabitants()}");

                $this->entityManager->flush($room);

                break;

            default:
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