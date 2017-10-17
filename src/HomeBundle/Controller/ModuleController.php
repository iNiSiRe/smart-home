<?php

namespace HomeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/api/v1/modules/{id}")
 */
class ModuleController extends Controller
{
    /**
     * @Route("/reset")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     */
    public function resetAction(Request $request, $id)
    {
        $client = $this->get('home.client.storage')->get($id);

        if (!$client) {
            throw new NotFoundHttpException();
        }

        $client->getConnection()->send(json_encode([
            'method' => 'post',
            'action' => '/api/v1/reset',
            'data' => []
        ]));

        return new JsonResponse(['status' => true]);
    }

    /**
     * @Route("/update")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $id)
    {
        $client = $this->get('home.client.storage')->get($id);

        if (!$client) {
            throw new NotFoundHttpException();
        }

        $module = $this->get('doctrine.orm.entity_manager')->getRepository('HomeBundle:Module')->find($id);

        if (!$module) {
            throw new NotFoundHttpException();
        }

        $client->getConnection()->send(json_encode([
            'method' => 'post',
            'action' => '/api/v1/firmware',
            'data' => [
                'file' => $module->getFirmware()->getFile(),
                'version' => $module->getFirmware()->getVersion()
            ]
        ]));

        return new JsonResponse(['status' => true]);
    }
}