<?php

namespace CommonBundle\Handler;

use BinSoul\Net\Mqtt\Message;

/**
 * Class AbstractHandler
 * @package CommonBundle\Handler
 */
abstract class AbstractHandler
{
    /**
     * @return string
     */
    abstract function getTopic();

    /**
     * @param Message $message
     *
     * @return void
     */
    abstract function onMessage(Message $message);
}