<?php


namespace HomeBundle\Service;

use HomeBundle\Listener\DataStorageListener;
use inisire\ReactBundle\EventDispatcher\AsynchronousEventDispatcher;

class DataStorage
{
    /**
     * @var AsynchronousEventDispatcher
     */
    private $dispatcher;

    /**
     * @param AsynchronousEventDispatcher $dispatcher
     */
    public function __construct()
    {

    }

    /**
     * @param string $type
     * @param array  $data
     *
     * @throws \Exception
     */
    public function store(string $type, array $data)
    {
        $this->dispatcher->dispatch(DataStorageListener::EVENT_DATA_STORAGE_STORE, $data);
    }
}