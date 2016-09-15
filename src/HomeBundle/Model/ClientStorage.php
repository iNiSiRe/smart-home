<?php

namespace HomeBundle\Model;

use Ratchet\ConnectionInterface;

class ClientStorage
{
    /**
     * @var Client[]
     */
    private $clients;

    /** @var \SplObjectStorage */
    private $storage;

    /**
     * ClientStorage constructor.
     */
    public function __construct()
    {
        $this->storage = new \SplObjectStorage();
    }

    /**
     * @param Client $client
     */
    public function add(Client $client)
    {
        $this->clients[$client->getId()] = $client;
        $this->storage->attach($client->getConnection(), $client);
    }

    /**
     * @param ConnectionInterface $connection
     *
     * @return Client
     */
    public function getByConnection(ConnectionInterface $connection)
    {
        return $this->storage->offsetGet($connection);
    }

    /**
     * @param $id
     *
     * @return Client|null
     */
    public function get($id)
    {
        if (!isset($this->clients[$id])) {
            return null;
        }

        return $this->clients[$id];
    }
}