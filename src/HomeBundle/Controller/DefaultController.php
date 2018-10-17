<?php

namespace HomeBundle\Controller;

use HomeBundle\Application\InhabitantsMonitorApplication;
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
}
