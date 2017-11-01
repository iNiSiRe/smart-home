<?php

namespace HomeBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class BoilerUnit extends Unit
{
    /**
     * @var float
     */
    private $temperature;

    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var TemperatureHumidityUnit[]
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
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return BoilerUnit
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

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
}