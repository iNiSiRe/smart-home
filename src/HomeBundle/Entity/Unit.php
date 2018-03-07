<?php

namespace HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensor
 *
 * @ORM\Table(name="units")
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="integer")
 * @ORM\DiscriminatorMap({1 = "Unit", 2 = "SwitchUnit", 3 = "BeamIntersectionSensor", 4 = "TemperatureHumidityUnit", 5 = "BoilerUnit"})
 */
class Unit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255)
     */
    private $class;

    /**
     * @var Module
     *
     * @ORM\ManyToOne(targetEntity="HomeBundle\Entity\Module", inversedBy="units")
     */
    private $module;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="HomeBundle\Entity\Room", inversedBy="units")
     */
    private $room;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $deviceId;

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     *
     * @return Unit
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Unit
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return Unit
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set unit
     *
     * @param Module $module
     *
     * @return Unit
     */
    public function setModule(Module $module = null)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get unit
     *
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set room
     *
     * @param Room $room
     *
     * @return Unit
     */
    public function setRoom(Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s#%s', $this->name, $this->id);
    }

    /**
     * @return string
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param string $deviceId
     *
     * @return Unit
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }
}
