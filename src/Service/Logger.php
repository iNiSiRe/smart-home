<?php


namespace Service;


use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Stream\WritableResourceStream;

class Logger implements LoggerInterface
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
     *
     * @throws \Exception
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

    /**
     * @inheritdoc
     */
    public function emergency($message, array $context = array())
    {
        $this->log('debug', $message);
    }

    /**
     * @inheritdoc
     */
    public function alert($message, array $context = array())
    {
        $this->log('debug', $message);
    }

    /**
     * @inheritdoc
     */
    public function critical($message, array $context = array())
    {
        $this->log('debug', $message);
    }

    /**
     * @inheritdoc
     */
    public function error($message, array $context = array())
    {
        $this->log('debug', $message);
    }

    /**
     * @inheritdoc
     */
    public function warning($message, array $context = array())
    {
        $this->log('debug', $message);
    }

    /**
     * @inheritdoc
     */
    public function notice($message, array $context = array())
    {
        $this->log('debug', $message);
    }

    /**
     * @inheritdoc
     */
    public function info($message, array $context = array())
    {
        $this->log('debug', $message);
    }

    /**
     * @inheritdoc
     */
    public function debug($message, array $context = array())
    {
        $this->log('debug', $message);
    }

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = array())
    {
        $this->write($level, $message);
    }
}