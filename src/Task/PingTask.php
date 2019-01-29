<?php

namespace Task;

use Broker\Task;

class PingTask implements Task
{
    public $ips;

    /**
     * PingTask constructor.
     *
     * @param $ips
     */
    public function __construct($ips)
    {
        $this->ips = $ips;
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return 'rpc.ping';
    }
}