<?php

namespace HomeBundle\MessageProcessor;

class MessageProcessorFactory
{
    /**
     * @var MessageProcessorInterface[]
     */
    private $processors;

    public function register(MessageProcessorInterface $messageProcessor)
    {
        $this->processors[$messageProcessor->getMessageType()] = $messageProcessor;
    }

    /**
     * @param $type
     *
     * @return MessageProcessorInterface
     */
    public function create($type)
    {
        if (!isset($this->processors[$type])) {
            throw new \InvalidArgumentException("Message processor for type {$type} doesn't exists");
        }

        return $this->processors[$type];
    }
}