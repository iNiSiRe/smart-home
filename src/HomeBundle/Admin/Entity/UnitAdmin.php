<?php

namespace HomeBundle\Admin\Entity;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UnitAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name')
            ->add('class')
            ->add('mode')
            ->add('pin')
            ->add('value')
            ->add('module')
            ->add('room')
            ->add('type')
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('class')
            ->add('value')
        ;
    }

}