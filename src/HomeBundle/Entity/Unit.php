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
     *
     * @throws \ReflectionException
     */
    public function getClass()
    {
        return (new \ReflectionClass($this))->getShortName();
    }
}
