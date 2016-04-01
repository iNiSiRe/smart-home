<?php
/**
 * Created by PhpStorm.
 * User: iNiSiRe
 * Date: 01.04.2016
 * Time: 2:12
 */

namespace HomeBundle\Event;


use HomeBundle\Entity\Sensor;
use Symfony\Component\EventDispatcher\Event;

class SensorEvent extends Event
{
    /**
     * @var Sensor
     */
    protected $sensor;

    /**
     * @var string
     */
    protected $value;

    /**
     * SensorEvent constructor.
     *
     * @param Sensor $sensor
     * @param        $value
     */
    public function __construct(Sensor $sensor, $value)
    {
        $this->sensor = $sensor;
        $this->value = $value;
    }

    /**
     * @return Sensor
     */
    public function getSensor()
    {
        return $this->sensor;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}