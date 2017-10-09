<?php

namespace CommonBundle\Bridge;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

class WebSocketHttpBridge
{
    /**
     * @var Router
     */
    private $router;

    /**
     * WebSocketHttpBridge constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function handleRequest()
    {

    }
}