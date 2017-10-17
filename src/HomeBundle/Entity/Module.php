<?php

namespace HomeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     * @var Unit[]
     *
     * @ORM\OneToMany(targetEntity="HomeBundle\Entity\Unit", mappedBy="module", indexBy="name")
     */
    private $units;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="HomeBundle\Entity\Room")
     */
    private $room;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $mac;

    /**
     * @var Firmware
     *
     * @ORM\Embedded(class="HomeBundle\Entity\Firmware")
     */
    private $firmware;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $config;

    /**
     * @var LogRecord[]
     *
     * @ORM\OneToMany(targetEntity="HomeBundle\Entity\LogRecord", mappedBy="module")
     */
    private $logs;

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
     * Constructor
     */
    public function __construct()
    {
        $this->units = new ArrayCollection();
        $this->logs = new ArrayCollection();
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
     * @return Unit[]
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
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * @param string $mac
     *
     * @return Module
     */
    public function setMac($mac)
    {
        $this->mac = $mac;
        
        return $this;
    }

    /**
     * @return Firmware
     */
    public function getFirmware()
    {
        return $this->firmware;
    }

    /**
     * @param Firmware $firmware
     *
     * @return $this
     */
    public function setFirmware($firmware)
    {
        $this->firmware = $firmware;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '#' . (string) $this->id;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return Module
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return LogRecord[]
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param LogRecord[] $logs
     *
     * @return Module
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;

        return $this;
    }

    /**
     * @param $log
     *
     * @return $this
     */
    public function addLog($log)
    {
        $this->logs->add($log);

        return $this;
    }

    /**
     * @param $log
     *
     * @return $this
     */
    public function removeLog($log)
    {
        $this->logs->removeElement($log);

        return $this;
    }
}