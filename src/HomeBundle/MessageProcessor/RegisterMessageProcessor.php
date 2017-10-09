<?php

namespace HomeBundle\MessageProcessor;

use HomeBundle\Actions;
use HomeBundle\Entity\Module;
use HomeBundle\Entity\Unit;
use Ratchet\ConnectionInterface;

/**
 * Class RegisterMessageProcessor
 *
 * @package HomeBundle\MessageProcessor
 */
class RegisterMessageProcessor extends AbstractMessageProcessor
{
    /**
     * @param $connection
     * @param $message
     */
    public function process(ConnectionInterface $connection, $message)
    {
        $module = new Module();

        $this->entityManager->persist($module);

        // Units
        foreach ($message['units'] as $data) {

            $unit = (new Unit())
                ->setType($data['type'] == 'sensor' ? Unit::TYPE_OUTPUT : Unit::TYPE_INPUT)
                ->setName($data['name'])
                ->setClass($data['class'])
                ->setModule($module)
            ;

            $this->entityManager->persist($unit);

            $module->addUnit($unit);
        }

        $this->entityManager->flush($module);

        $connection->send(json_encode([
            'type' => 2, // response
            'action' => Actions::ACTION_REGISTER,
            'success' => true,
            'id' => $module->getId()
        ]));

        $this->logger->info("Module #{$module->getId()} register success");
    }

    public function getMessageType()
    {
        return Actions::ACTION_REGISTER;
    }
}