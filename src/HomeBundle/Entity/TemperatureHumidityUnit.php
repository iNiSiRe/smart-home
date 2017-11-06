<?php

namespace HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class TemperatureHumidityUnit extends Unit
{
    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $temperature;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $humidity;

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
     * @return TemperatureHumidityUnit
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * @return float
     */
    public function getHumidity()
    {
        return $this->humidity;
    }

    /**
     * @param float $humidity
     *
     * @return TemperatureHumidityUnit
     */
    public function setHumidity($humidity)
    {
        $this->humidity = $humidity;

        return $this;
    }
}