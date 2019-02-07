<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 2/7/19
 * Time: 1:55 AM
 */

namespace Service;


use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Stream\ThroughStream;

class FFmpegWatcher
{
    /**
     * @var ThroughStream
     */
    private $stream;

    /**
     * @var Process
     */
    private $ffmpeg;

    /**
     * @var int
     */
    private $received = 0;

    /**
     * @var int
     */
    private $uptime = 0;

    /**
     * @var int
     */
    private $restarts = 0;

    /**
     * @var string
     */
    private $cmd;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var bool
     */
    private $working = false;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct($cmd, LoopInterface $loop, Logger $logger)
    {
        $this->cmd = $cmd;
        $this->loop = $loop;
        $this->stream = new ThroughStream();
        $this->logger = $logger;

        $this->loop->addPeriodicTimer(5, function () {

            $this->working = $this->received > 0;
            $this->received = 0;
            $this->uptime += 5;

        });

        $loop->addPeriodicTimer(10, function () {

            if (!$this->working) {

                $this->logger->write('error', "ffmpeg isn't working, trying to restart");

                $this->restarts++;
                $this->uptime = 0;

                if ($this->ffmpeg !== null) {
                    $this->ffmpeg->terminate(9);
                    $this->ffmpeg->close();
                    $this->ffmpeg = null;
                    $this->start();
                }

            }

        });
    }

    public function start()
    {
        $this->ffmpeg = new Process($this->cmd);
        $this->ffmpeg->start($this->loop);

        $this->ffmpeg->stdout->on('data', function ($chunk) {

            $this->received += strlen($chunk);
            $this->stream->write($chunk);

        });
    }

    /**
     * @return bool
     */
    public function isWorking()
    {
        return $this->working;
    }

    public function getStatus()
    {
        return [
            'working' => $this->working,
            'uptime' => $this->uptime,
            'restarts' => $this->restarts
        ];
    }

    /**
     * @return ThroughStream
     */
    public function getStream()
    {
        return $this->stream;
    }
}