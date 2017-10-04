<?php

namespace CommonBundle\Subscriber;

use CommonBundle\Service\FileUploader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use HomeBundle\Entity\Module;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DoctrineSubscriber implements EventSubscriber
{
    /**
     * @var FileUploader
     */
    private $uploader;

    /**
     * DoctrineSubscriber constructor.
     *
     * @param FileUploader $uploader
     */
    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove
        );
    }

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate($event)
    {
        $object = $event->getObject();

        if (!$object instanceof Module) {
            return;
        }

        $file = $object->getFirmware()->getFile();

        if (!$file instanceof UploadedFile) {
            return;
        }

        $filename = $this->uploader->upload($file);
        $object->getFirmware()->setFile($filename);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist($event)
    {
        $object = $event->getObject();

        if (!$object instanceof Module) {
            return;
        }

        $file = $object->getFirmware()->getFile();

        if (!$file instanceof UploadedFile) {
            return;
        }

        $filename = $this->uploader->upload($file);
        $object->getFirmware()->setFile($filename);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postRemove($event)
    {
        $object = $event->getObject();

        if (!$object instanceof Module) {
            return;
        }

        $file = $object->getFirmware()->getFile();

        if (empty($file)) {
            return;
        }

        $this->uploader->remove($file);
        $object->getFirmware()->setFile(null);
    }
}
