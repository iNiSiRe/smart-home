<?php


namespace HomeBundle\Service;


use Doctrine\ORM\EntityManager;
use HomeBundle\Entity\Module;
use React\EventLoop\LoopInterface;

class FirmwareObserver
{
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var LoopInterface
     */
    private $loop;
    /**
     * @var FirmwareUpdater
     */
    private $updater;

    /**
     * @param EntityManager   $manager
     * @param LoopInterface   $loop
     * @param FirmwareUpdater $updater
     */
    public function __construct(EntityManager $manager, LoopInterface $loop, FirmwareUpdater $updater)
    {
        $this->manager = $manager;
        $this->loop = $loop;
        $this->updater = $updater;
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function observe()
    {
        // Update

        $modules = $this->manager->getRepository('HomeBundle:Module')
            ->findBy(['status' => Module::STATUS_READY]);

        $firmware = $this->manager->getRepository('HomeBundle:Firmware')
            ->findOneBy([], ['version' => 'DESC']);

        foreach ($modules as $module) {

            if ($module->getFirmware()->getVersion() == $firmware->getVersion()) {
                continue;
            }

            $module->setStatus(Module::STATUS_UPDATING);
            $this->manager->flush($module);

            if ($this->updater->update($module, $firmware)) {
                $module->setFirmware($firmware);
                $module->setStatus(Module::STATUS_UPDATE_NOT_COMMITTED);
                $module->setUpdatedAt(new \DateTime());
                $this->manager->flush($module);
            }
        }


        // Commit

        $modules = $this->manager->getRepository('HomeBundle:Module')
            ->findBy(['status' => Module::STATUS_UPDATE_NOT_COMMITTED]);

        $now = new \DateTime();

        foreach ($modules as $module) {

            if ($now->getTimestamp() - $module->getUpdatedAt() < 300) {
                continue;
            }

            if ($this->updater->commit($module)) {
                $module->setStatus(Module::STATUS_READY);
                $this->manager->flush($module);
            }
        }
    }

    public function start()
    {
        $this->loop->addPeriodicTimer(60, [$this, 'observe']);
    }
}