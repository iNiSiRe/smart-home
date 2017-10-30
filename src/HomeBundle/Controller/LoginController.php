<?php

namespace HomeBundle\Controller;

use HomeBundle\Entity\Unit;
use HomeBundle\Form\LoginType;
use HomeBundle\Model\Client;
use Ratchet\ConnectionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @Route("/api/v1/login")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $login = $form->getData();

            $module = $this->getDoctrine()
                ->getRepository('HomeBundle:Module')
                ->findOneBy(['mac' => $login->getMAC()]);

            if (!$module) {
                return $this->get('response.builder')->build(Response::HTTP_BAD_REQUEST);
            }

            $units = [];

            /**
             * @var Unit $unit
             */
            foreach ($module->getUnits() as $unit) {
                $units[] = [
                    'id' => $unit->getId(),
                    'class' => $unit->getClass(),
                    'config' => $unit->getConfig(),
                    'variables' => $unit->getVariables()
                ];
            }


            $this->get('home.client.storage')->add(new Client($connection, 0, $module->getId()));

            $connection->send(json_encode([
                'method' => 'post',
                'action' => '/api/v1/config',
                'data' => $module->getConfig()
            ]));

            $connection->send(json_encode([
                'method' => 'post',
                'action' => '/api/v1/units',
                'data' => $units
            ]));

            $this->get('logger')->info("Module #{$module->getId()} login success");
        }

        return $this->get('response.builder')->build();
    }
}