<?php

namespace HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="firmwares")
 */
class Firmware
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $version;

    /**
     * @var string|UploadedFile
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\File(mimeTypes={"application/octet-stream"})
     */
    private $file;

    /**
     * @var UploadedFile
     */
    private $uploadedFile;

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return Firmware
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string|UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return Firmware
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @return $this
     */
    public function setUploadedFile(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Firmware
     */
    public function setId(int $id): Firmware
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'Firmware#' . (string) $this->getVersion();
    }
}