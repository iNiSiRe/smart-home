<?php


namespace HomeBundle\Service;

use HomeBundle\Task\ElasticWriteTask;
use inisire\ReactBundle\Threaded\Pool;

class DataStorage
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @param string $type
     * @param array  $data
     *
     * @throws \Exception
     */
    public function store($type, array $data)
    {
        $this->pool->submit(new ElasticWriteTask($data));
    }
}