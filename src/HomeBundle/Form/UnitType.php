<?php

namespace HomeBundle\Form;

use HomeBundle\Entity\Unit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnitType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value');
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults(['data_class' => Unit::class]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'unit';
    }

}