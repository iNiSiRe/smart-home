<?php

namespace CommonBundle\Bridge;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class WebSocketHttpBridge
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * WebSocketHttpBridge constructor.
     *
     * @param Router             $router
     * @param ContainerInterface $container
     */
    public function __construct(Router $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    public function handleRequest(Request $request)
    {
        $this->router->getContext()->fromRequest($request);
        $array = $this->router->matchRequest($request);

        list ($controller, $action) = explode('::', $array['_controller']);

        $instance = new $controller;

        if ($instance instanceof ContainerAwareInterface) {
            $instance->setContainer($this->container);
        }

        $response = $instance->$action($request);
    }
}