<?php

namespace CommonBundle\Server;

use React\EventLoop\LoopInterface;
use React\Http\Server;

class HttpServer extends Server
{
    /**
     * @var \React\Socket\Server
     */
    protected $socket;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * HttpServer constructor
     *
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->socket = new \React\Socket\Server($loop);
        parent::__construct($this->socket);
        $this->loop = $loop;
    }

    /**
     * @return \React\Socket\Server
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }
}