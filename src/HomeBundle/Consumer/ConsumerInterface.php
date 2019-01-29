<?php


namespace HomeBundle\Consumer;


interface ConsumerInterface
{
    /**
     * @param $task
     *
     * @return boolean
     */
    public function isSupport($task);

    public function consume($task);
}