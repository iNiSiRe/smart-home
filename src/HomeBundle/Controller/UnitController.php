<?php

namespace HomeBundle\Controller;

use Doctrine\ORM\EntityRepository;
use HomeBundle\Entity\Unit;
use HomeBundle\Form\UnitType;
use HomeBundle\Transformer\UnitTransformer;
use PrivateDev\Utils\Controller\CRUDController;
use PrivateDev\Utils\Fractal\TransformerAbstract;
use PrivateDev\Utils\Json\TransformableJsonResponseBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;

/**
 * @Route("/api/v1/units")
 */
class UnitController extends CRUDController
{

    /**
     * Get repository of the Entity
     *
     * @return EntityRepository
     */
    protected function getEntityRepository()
    {
        return $this->getDoctrine()->getRepository('HomeBundle:Unit');
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
        return $this->createForm(UnitType::class, $entity, $options);
    }

    /**
     * Create transformer for the Entity
     *
     * @return TransformerAbstract
     */
    protected function createEntityTransformer()
    {
        return new UnitTransformer();
    }

    /**
     * Create an empty Entity
     *
     * @return object
     */
    protected function createEntity()
    {
        return new Unit();
    }

    /**
     * @return TransformableJsonResponseBuilder
     */
    protected function getResponseBuilder()
    {
        return $this->get('response.builder');
    }
}