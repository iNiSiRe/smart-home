<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 14.09.16
 * Time: 0:10
 */

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

        foreach ($module->getUnits() as $unit) {

            if ($unit->getType() !== Unit::TYPE_CONTROLLER) {
                continue;
            }

            $this->logger->info('Add listener to input');

            $listener = function ($id, $unit, $value) use ($connection) {

                $this->logger->info('Perform listener');

                $connection->send(json_encode([
                    'action' => Actions::ACTION_CONTROL,
                    'resource' => 'input',
                    'unit' => $unit,
                    'value' => $value
                ]));
            };

            $this->emitter->on(
                sprintf(
                    'unit.%s.%s.command.control',
                    $module->getId(),
                    $unit->getName()
                ),
                $listener
            );
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