<?php

namespace CommonBundle\Bridge;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

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

    public function handleRequest(Request $request)
    {
        $array = $this->router->matchRequest($request);
    }
}