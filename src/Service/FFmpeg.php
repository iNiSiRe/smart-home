<?php

namespace Service;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;

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

    /**
     * @var int
     */
    private $restarts = 0;

    /**
     * @var int
     */
    private $uptime = 0;

    /**
     * @inheritdoc
     */
    public function start(LoopInterface $loop, $interval = 0.1)
    {
        parent::start($loop, $interval);

        if (!$this->monitor) {

            $this->monitor = $loop->addPeriodicTimer(1, function () use ($loop, $interval) {

                $this->working = $this->received > 0;
                $this->received = 0;
                $this->uptime++;

            });

            $loop->addPeriodicTimer(30, function () use ($loop, $interval) {

                if (!$this->working) {
                    $this->restarts++;
                    $this->uptime = 0;
                    $this->close();
                    $this->start($loop, $interval);
                }

            });

        }

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

    public function getStatus()
    {
        return [
            'working' => $this->working,
            'uptime' => $this->uptime,
            'restarts' => $this->restarts
        ];
    }
}