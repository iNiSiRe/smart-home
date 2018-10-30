<?php


namespace HomeBundle\Handler;

use BinSoul\Net\Mqtt\Message;
use CommonBundle\Handler\AbstractHandler;
use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\LogRecord;
use HomeBundle\Entity\Module;
use HomeBundle\Service\DataStorage;
use inisire\ReactBundle\Threaded\MonitoredPool;
use inisire\ReactBundle\Threaded\ServiceMethodCall;

class ModuleLogHandler extends AbstractHandler
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var MonitoredPool
     */
    private $pool;

    /**
     * @param Module        $module
     * @param MonitoredPool $pool
     */
    public function __construct(Module $module, MonitoredPool $pool)
    {
        $this->module = $module;
        $this->pool = $pool;
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
        if (!preg_match('#^(\w+)\s(\d+)\s([\d\.]+)\s(\d+)\|(\w+)\s+(.+)$#', $message->getPayload(), $matches)) {
            return;
        }

        $this->pool->submit(new ServiceMethodCall(DataStorage::class, 'store', ['log', [
            'type' => 'stdout',
            'module' => $this->module->getId(),
            'level' => $matches[4],
            'method' => $matches[5],
            'content' => $matches[6]
        ]]));
    }
}