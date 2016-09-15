<?php

namespace HomeBundle\MessageProcessor;

use Ratchet\ConnectionInterface;

interface MessageProcessorInterface
{
    public function process(ConnectionInterface $connection, $message);

    public function getMessageType();
}