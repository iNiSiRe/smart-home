<?php

namespace HomeBundle\Form;

use HomeBundle\Model\Login;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("MAC");
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Login::class]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'login';
    }

}