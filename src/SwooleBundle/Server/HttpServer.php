<?php


namespace SwooleBundle\Server;


use Swoole\Http\Server;
use SwooleBundle\Handler\RequestHandler;

class HttpServer extends Server
{
    /**
     * @var RequestHandler
     */
    protected $handler;

    /**
     * @param RequestHandler $handler
     */
    public function setRequestHandler(RequestHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritdoc
     */
    public function start()
    {
        $this->on('request', [$this->handler, 'handle']);

        parent::start();
    }
}