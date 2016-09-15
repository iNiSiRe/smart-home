<?php

namespace HomeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HomeBundle\Entity\Room;
use HomeBundle\Entity\Unit;

/**
 * Unit
 *
 * @ORM\Table(name="modules")
 * @ORM\Entity()
 */
class Module
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var Unit[]
     *
     * @ORM\OneToMany(targetEntity="HomeBundle\Entity\Unit", mappedBy="module", indexBy="name")
     */
    private $units;

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
     * @return Module
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
     * @return Module
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
     * @return Module
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
        $this->units = new ArrayCollection();
    }

    /**
     * Add unit
     *
     * @param Unit $unit
     *
     * @return Module
     */
    public function addUnit(Unit $unit)
    {
        $this->units[] = $unit;

        return $this;
    }

    /**
     * Remove unit
     *
     * @param Unit $unit
     */
    public function removeUnit(Unit $unit)
    {
        $this->units->removeElement($unit);
    }

    /**
     * Get sensors
     *
     * @return Collection[]|Unit[]
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasUnit($name)
    {
        return isset($this->units[$name]);
    }

    /**
     * @param $name
     *
     * @return \HomeBundle\Entity\Unit|mixed|null
     */
    public function getUnit($name)
    {
        return $this->hasUnit($name)
            ? $this->units[$name]
            : null;
    }

    /**
     * Set room
     *
     * @param Room $room
     *
     * @return Module
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
        return '#' . (string) $this->id;
    }
}
