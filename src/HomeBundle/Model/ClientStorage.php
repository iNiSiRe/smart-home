<?php

namespace HomeBundle\Model;

class ClientStorage
{
    /**
     * @var Client[]
     */
    private $clients;

    /**
     * @param Client $client
     */
    public function add(Client $client)
    {
        $this->clients[$client->getId()] = $client;
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