<?php

namespace HomeBundle\MessageProcessor;

use Doctrine\ORM\EntityManager;
use Evenement\EventEmitter;
use HomeBundle\Model\ClientStorage;
use Monolog\Logger;

abstract class AbstractMessageProcessor implements MessageProcessorInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ClientStorage
     */
    protected $clientStorage;

    /**
     * @var EventEmitter
     */
    protected $emitter;

    /**
     * RegisterMessageProcessor constructor.
     *
     * @param Logger $logger
     * @param EntityManager $entityManager
     * @param ClientStorage $clientStorage
     * @param EventEmitter $emitter
     */
    public function __construct(Logger $logger, EntityManager $entityManager, ClientStorage $clientStorage, EventEmitter $emitter)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->clientStorage = $clientStorage;
        $this->emitter = $emitter;
    }
}