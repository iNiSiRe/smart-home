<?php

namespace CommonBundle\Bridge;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebSocketHttpBridge
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * WebSocketHttpBridge constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleRequest(Request $request)
    {
        return $this->container->get('kernel')->handle($request);

//        $this->container->get('request_stack')->push($request);
//
//        $this->router->getContext()->fromRequest($request);
//        $array = $this->router->matchRequest($request);
//
//        list ($controller, $action) = explode('::', $array['_controller']);
//
//        $instance = new $controller;
//
//        if ($instance instanceof ContainerAwareInterface) {
//            $instance->setContainer($this->container);
//        }
//
//        $this->container->get('request_stack')->pop();

//        return $instance->$action($request);
    }
}