<?php

namespace HomeBundle\Controller;

use Doctrine\ORM\EntityRepository;
use HomeBundle\Entity\Firmware;
use HomeBundle\Form\FirmwareType;
use HomeBundle\Transformer\FirmwareTransformer;
use PrivateDev\Utils\Controller\CRUDLController;
use PrivateDev\Utils\Fractal\TransformerAbstract;
use PrivateDev\Utils\Json\TransformableJsonResponseBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;

/**
 * @Route("/firmwares")
 */
class FirmwareController extends CRUDLController
{
    /**
     * Get repository of the Entity
     *
     * @return EntityRepository
     */
    protected function getEntityRepository()
    {
        return $this->get('doctrine.orm.entity_manager')->getRepository('HomeBundle:Firmware');
    }

    /**
     * Create Form for the Entity
     *
     * @param object $entity
     * @param array $options
     *
     * @return FormInterface
     */
    protected function createEntityForm($entity, array $options = []): FormInterface
    {
        return $this->createForm(FirmwareType::class, $entity, $options);
    }

    /**
     * Create transformer for the Entity
     *
     * @return TransformerAbstract
     */
    protected function createEntityTransformer()
    {
        return new FirmwareTransformer();
    }

    /**
     * Create an empty Entity
     *
     * @return object
     */
    protected function createEntity()
    {
        return new Firmware();
    }

    /**
     * @return TransformableJsonResponseBuilder
     */
    protected function getResponseBuilder()
    {
        return $this->get('response.builder');
    }

    /**
     * @param Firmware $entity
     */
    public function onCreateSuccess($entity)
    {

    }
}