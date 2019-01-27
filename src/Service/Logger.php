<?php


namespace Service;


use React\EventLoop\LoopInterface;
use React\Stream\WritableResourceStream;

class Logger
{
    /**
     * @var WritableResourceStream
     */
    private $file;

    /**
     * @param               $file
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop, $file)
    {
        $handle = fopen($file, 'a');
        $this->file = new WritableResourceStream($handle, $loop);
    }

    /**
     * @param $level
     * @param $message
     */
    public function write($level, $message)
    {
        $this->file->write(
            sprintf(
                "%s [%s] %s" . PHP_EOL,
                (new \DateTime())->format(\DateTime::ATOM),
                $level,
                $message
            )
        );
    }
}