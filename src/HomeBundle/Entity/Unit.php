<?php

namespace HomeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HomeBundle\Entity\Room;
use HomeBundle\Entity\Sensor;

/**
 * Unit
 *
 * @ORM\Table(name="unit")
 * @ORM\Entity(repositoryClass="HomeBundle\Repository\UnitRepository")
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
     * @var bool
     *
     * @ORM\Column(name="connected", type="boolean", nullable=true)
     */
    private $connected;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, unique=true)
     */
    private $address;

    /**
     * @var Sensor[]
     *
     * @ORM\OneToMany(targetEntity="HomeBundle\Entity\Sensor", mappedBy="unit")
     */
    private $sensors;

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
     * Set connected
     *
     * @param boolean $connected
     *
     * @return Unit
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * Get connected
     *
     * @return bool
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Unit
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sensors = new ArrayCollection();
    }

    /**
     * Add sensor
     *
     * @param Sensor $sensor
     *
     * @return Unit
     */
    public function addSensor(Sensor $sensor)
    {
        $this->sensors[] = $sensor;

        return $this;
    }

    /**
     * Remove sensor
     *
     * @param Sensor $sensor
     */
    public function removeSensor(Sensor $sensor)
    {
        $this->sensors->removeElement($sensor);
    }

    /**
     * Get sensors
     *
     * @return Collection
     */
    public function getSensors()
    {
        return $this->sensors;
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
}
