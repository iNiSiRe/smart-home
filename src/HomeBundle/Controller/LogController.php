<?php

namespace HomeBundle\Controller;

use Doctrine\ORM\EntityRepository;
use HomeBundle\Entity\LogRecord;
use HomeBundle\Form\LogType;
use HomeBundle\Transformer\DummyTransformer;
use PrivateDev\Utils\Controller\CRUDLController;
use PrivateDev\Utils\Fractal\TransformerAbstract;
use PrivateDev\Utils\Json\TransformableJsonResponseBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/api/v1/modules/self/logs")
 */
class LogController extends CRUDLController
{

    /**
     * Get repository of the Entity
     *
     * @return EntityRepository
     */
    protected function getEntityRepository()
    {
        return $this->getDoctrine()->getRepository('HomeBundle:LogRecord');
    }

    /**
     * Create Form for the Entity
     *
     * @param object $entity
     * @param array  $options
     *
     * @return FormInterface
     */
    protected function createEntityForm($entity, array $options = []): FormInterface
    {
        return $this->createForm(LogType::class, $entity, $options);
    }

    /**
     * Create transformer for the Entity
     *
     * @return TransformerAbstract
     */
    protected function createEntityTransformer()
    {
        return new DummyTransformer();
    }

    /**
     * Create an empty Entity
     *
     * @return object
     */
    protected function createEntity()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $connection = $request->server->get('WS_CONNECTION');

        if (!$connection) {
            throw new AccessDeniedHttpException();
        }

        $client = $this->get('home.client.storage')->getByConnection($connection);

        if (!$client) {
            throw new AccessDeniedHttpException();
        }

        $module = $this->get('doctrine.orm.entity_manager')
            ->getRepository('HomeBundle:Module')
            ->find($client->getId());

        return (new LogRecord())
            ->setModule($module)
            ->setCreatedAt(new \DateTime());
    }

    /**
     * @return TransformableJsonResponseBuilder
     */
    protected function getResponseBuilder()
    {
        return $this->get('response.builder');
    }
}