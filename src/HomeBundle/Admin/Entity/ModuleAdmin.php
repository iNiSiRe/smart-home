<?php

namespace HomeBundle\Admin\Entity;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ModuleAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('id')
            ->add('name')
            ->add('units')
            ->add('room')
        ;
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('uid')
        ;
    }

}