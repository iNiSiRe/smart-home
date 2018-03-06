<?php

namespace HomeBundle\Form;

use HomeBundle\Entity\BoilerManualMode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoilerManualModeType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('enabled');
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults(['data_class' => BoilerManualMode::class]);
    }
}