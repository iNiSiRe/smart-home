<?php

namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;

class PingHandler extends AbstractHandler
{

    /**
     * @return string
     */
    function getTopic()
    {
        return 'ping';
    }

    /**
     * @param Message $message
     *
     * @return void
     */
    function onMessage(Message $message)
    {
        $data = json_decode($message->getPayload(), true);

        $source = $data['source'] ?? null;

        
    }
}