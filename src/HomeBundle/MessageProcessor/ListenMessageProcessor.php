<?php

namespace HomeBundle\MessageProcessor;

use HomeBundle\Actions;
use Ratchet\ConnectionInterface;

class ListenMessageProcessor extends AbstractMessageProcessor
{
    /**
     * @param ConnectionInterface $connection
     * @param $message
     */
    public function process(ConnectionInterface $connection, $message)
    {
        $room = $message['room'];
        foreach ($message['sensors'] as $unit) {
            $this->emitter->on(sprintf('sensor.%s.%s.update', $room, $unit), function ($room, $sensor, $value) use ($connection) {
                $connection->send(json_encode([
                    'room' => $room,
                    'sensor' => $sensor,
                    'value' => $value
                ]));
            });
        }
    }

    /**
     * @return int
     */
    public function getMessageType()
    {
        return Actions::ACTION_LISTEN;
    }
}