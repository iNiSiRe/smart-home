<?php

namespace HomeBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class BoilerUnit extends SwitchUnit
{
    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $temperature;

    /**
     * @var BoilerManualMode
     *
     * @ORM\Embedded(class="HomeBundle\Entity\BoilerManualMode")
     */
    private $manual;

    /**
     * @var TemperatureHumidityUnit[]
     *
     * @ORM\ManyToMany(targetEntity="HomeBundle\Entity\TemperatureHumidityUnit")
     */
    private $sensors;

    /**
     * @return float
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * @param float $temperature
     *
     * @return BoilerUnit
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * @return TemperatureHumidityUnit[]
     */
    public function getSensors()
    {
        return $this->sensors;
    }

    /**
     * @param TemperatureHumidityUnit[] $sensors
     *
     * @return BoilerUnit
     */
    public function setSensors($sensors)
    {
        $this->sensors = $sensors;

        return $this;
    }

    /**
     * @return BoilerManualMode
     */
    public function getManual()
    {
        return $this->manual;
    }

    /**
     * @param BoilerManualMode $manual
     *
     * @return BoilerUnit
     */
    public function setManual($manual)
    {
        $this->manual = $manual;

        return $this;
    }
}