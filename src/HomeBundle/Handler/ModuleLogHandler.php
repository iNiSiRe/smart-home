<?php


namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\LogRecord;
use HomeBundle\Entity\Module;

class ModuleLogHandler extends AbstractHandler
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @param Module        $module
     * @param EntityManager $manager
     */
    public function __construct(Module $module, EntityManager $manager)
    {
        $this->module = $module;
        $this->manager = $manager;
    }

    /**
     * @return string
     */
    function getTopic()
    {
        return 'modules/' . $this->module->getId() . '/stdout';
    }

    /**
     * @param Message $message
     *
     * @return void
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    function onMessage(Message $message)
    {
        $this->module->addLog(new LogRecord($message->getPayload()));
        $this->manager->persist($this->module);
        $this->manager->flush();
    }
}