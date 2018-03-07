<?php

namespace HomeBundle\Admin\Entity;

use Doctrine\DBAL\Types\JsonArrayType;
use HomeBundle\Entity\BeamIntersectionSensor;
use HomeBundle\Entity\BoilerManualMode;
use HomeBundle\Entity\BoilerUnit;
use HomeBundle\Entity\SwitchUnit;
use HomeBundle\Entity\TemperatureHumidityUnit;
use HomeBundle\Form\ArrayElementType;
use HomeBundle\Form\BoilerManualModeType;
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
        ;

        switch (true) {

            case ($this->getSubject() instanceof BoilerUnit): {
                $form->add('enabled');
                $form->add('temperature');
                $form->add('sensors');
                $form->add('manual', BoilerManualModeType::class);
            } break;

            case ($this->getSubject() instanceof SwitchUnit): {
                $form->add('enabled');
            } break;

            case ($this->getSubject() instanceof TemperatureHumidityUnit): {
                $form->add('temperature');
                $form->add('humidity');
            } break;

            case ($this->getSubject() instanceof BeamIntersectionSensor): {
                $form->add('roomFrom');
                $form->add('roomTo');
                $form->add('light');
            } break;

        }
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->add('class')
            ->add('ip')
            ->add('deviceId')
        ;
    }

}