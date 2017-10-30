<?php

namespace HomeBundle\Admin\Entity;

use Doctrine\DBAL\Types\JsonArrayType;
use HomeBundle\Form\ArrayElementType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UnitAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name')
            ->add('class')
            ->add('module')
            ->add('room')
            ->add('variables', TextType::class)
            ->add('config', TextType::class)
        ;

        $form->get('variables')->addModelTransformer(new CallbackTransformer(function ($data) {

            if (!$data) {
                return null;
            }

            return json_encode($data);

        }, function ($data) {

            if (!$data) {
                return null;
            }

            return json_decode($data, true);
        }));
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('class')
        ;
    }

}