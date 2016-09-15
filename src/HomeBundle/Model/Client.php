<?php

namespace HomeBundle\Model;

use Ratchet\ConnectionInterface;

class Client
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var int
     */
    private $status;

    /**
     * Client constructor.
     *
     * @param ConnectionInterface $connection
     * @param $status
     * @param $id
     */
    public function __construct(ConnectionInterface $connection, $status, $id)
    {
        $this->connection = $connection;
        $this->status = $status;
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}