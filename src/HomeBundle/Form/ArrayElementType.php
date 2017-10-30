<?php
/**
 * Created by PhpStorm.
 * User: user18
 * Date: 30.10.17
 * Time: 19:16
 */

namespace HomeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ArrayElementType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', TextType::class)
            ->add('value', TextType::class)
        ;
    }
}