<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 04.04.18
 * Time: 19:49
 */

namespace HomeBundle\Admin\Entity;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FirmwareAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('file', TextType::class, ['disabled' => true])
            ->add('version')
            ->add('uploadedFile', FileType::class)
        ;
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('file')
            ->add('version')
        ;
    }
}