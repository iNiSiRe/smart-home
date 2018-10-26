<?php

namespace HomeBundle\Controller;

use HomeBundle\Application\InhabitantsMonitorApplication;
use HomeBundle\Listener\DataStorageListener;
use HomeBundle\Service\DataStorage;
use inisire\ReactBundle\EventDispatcher\AsynchronousEventDispatcher;
use inisire\ReactBundle\Threaded\ServiceMethodCall;
use inisire\ReactBundle\Threaded\MonitoredPool;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/inhabitants", methods={"GET"})
     *
     * @param InhabitantsMonitorApplication $inhabitantsMonitorApplication
     *
     * @return JsonResponse
     */
    public function inhabitants(InhabitantsMonitorApplication $inhabitantsMonitorApplication)
    {
        return new JsonResponse([
            'success' => true,
            'data' => $inhabitantsMonitorApplication->getInhabitants()
        ]);
    }

    /**
     * @Route("test", methods={"GET"})
     *
     * @param MonitoredPool $pool
     *
     * @return JsonResponse
     */
    public function test(MonitoredPool $pool, DataStorage $storage)
    {
        $storage->store('1', ['1']);

        for ($i = 0; $i < 5; $i++) {
            $pool->submit(new ServiceMethodCall(DataStorage::class, 'store', [ '', ['test' => 1] ]));
        }

        return new JsonResponse([
            'success' => true,
            'status' => $pool->getStatus()
        ]);
    }

    /**
     * @Route("/status", methods={"GET"})
     *
     * @param MonitoredPool $pool
     *
     * @return JsonResponse
     */
    public function status(MonitoredPool $pool)
    {
        return new JsonResponse([
            'success' => true,
            'status' => $pool->getStatus()
        ]);
    }
}
