<?php

namespace HomeBundle\Controller;

use HomeBundle\Listener\TestListener;
use inisire\ReactBundle\EventDispatcher\AsynchronousEventDispatcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $rooms = $this->get('doctrine.orm.entity_manager')->getRepository('HomeBundle:Room')->findAll();

        return $this->render('HomeBundle:Default:index.html.twig', ['rooms' => $rooms]);
    }

    /**
     * @Route("/debug")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function debugAction()
    {
        return $this->render('@Home/Default/debug.html.twig');
    }

    /**
     * @Route("/message")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function messageAction(Request $request)
    {
        $this->get('logger')->debug('controller 1');

        $rooms = $this->get('doctrine.orm.entity_manager')->getRepository('HomeBundle:Room')->findAll();

        $dispatcher = $this->container->get(AsynchronousEventDispatcher::class);

        $this->get('logger')->debug('controller 2');

        $dispatcher->dispatch('test', new Event());

        $this->get('logger')->debug('controller 3');

        return new JsonResponse(['success' => true, 'class' => get_class($this->container->get('react.loop')), 'rooms' => count($rooms)]);
    }
}
