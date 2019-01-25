<?php

namespace Service;

use React\ChildProcess\Process;

class FFmpeg extends Process
{
    /**
     * @var bool
     */
    private $working = false;

    /**
     * @var int
     */
    private $received = 0;

    /**
     * @var \React\EventLoop\Timer\TimerInterface
     */
    private $monitor;

    public function start(\React\EventLoop\LoopInterface $loop, $interval = 0.1)
    {
        parent::start($loop, $interval);

        if (!$this->monitor) {

            $this->monitor = $loop->addPeriodicTimer(1, function () {
                $this->working = $this->received > 0;
                $this->received = 0;
            });

        }

        $this->on('exit', function () use ($loop) {

            $this->terminate(SIGTERM);
            $this->removeAllListeners();
            $this->start($loop);

        });

        $this->stdout->on('data', function ($chunk) use (&$received) {
            $this->received += strlen($chunk);
        });
    }

    /**
     * @return bool
     */
    public function isWorking()
    {
        return $this->working;
    }
}