<?php


namespace Task;

class ElasticWriteTask
{
    /**
     * @var array
     */
    public $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
}