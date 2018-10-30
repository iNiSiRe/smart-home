<?php

namespace HomeBundle\Application\Task;

class PingTask
{
    public $id;

    private $ips;

    private $result;

    private $completed;

    /**
     * PingTask constructor.
     *
     * @param $ips
     */
    public function __construct($ips)
    {
        $this->ips = $ips;
    }
}