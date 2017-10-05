<?php

namespace HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable()
 */
class Firmware
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $version;

    /**
     * @var string|UploadedFile
     *
     * @ORM\Column(type="string")
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
}