<?php


namespace Broker;


interface Task
{
    /**
     * @return string
     */
    public function getQueue();
}