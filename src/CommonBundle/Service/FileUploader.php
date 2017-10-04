<?php

namespace CommonBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
     * @var string
     */
    private $targetDir;

    /**
     * FileUploader constructor.
     *
     * @param $targetDir
     */
    public function __construct($targetDir)
    {
        if (substr($targetDir, -1, 1) != '/') {
            $targetDir .= '/';
        }

        $this->targetDir = $targetDir;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $hash = md5(uniqid());
        $path = substr($hash, 2, 0) . '/' . substr($hash, 2, 2) . '/';
        $fileName = $hash . '.' . $file->guessExtension();

        $file->move($this->getTargetDir() . $path, $fileName);

        return $path . $fileName;
    }

    /**
     * @param string $file
     */
    public function remove(string $file)
    {

    }

    /**
     * @return string
     */
    public function getTargetDir()
    {
        return $this->targetDir;
    }
}