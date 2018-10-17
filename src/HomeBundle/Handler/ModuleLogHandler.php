<?php


namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\LogRecord;
use HomeBundle\Entity\Module;
use HomeBundle\Service\DataStorage;

class ModuleLogHandler extends AbstractHandler
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var DataStorage
     */
    private $storage;

    /**
     * @param Module        $module
     * @param DataStorage   $storage
     */
    public function __construct(Module $module, DataStorage $storage)
    {
        $this->module = $module;
        $this->storage = $storage;
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
     */
    function onMessage(Message $message)
    {
        $this->storage->store('log', [
            'type' => 'stdout',
            'module' => $this->module->getId(),
            'content' => $message->getPayload()
        ]);
    }
}