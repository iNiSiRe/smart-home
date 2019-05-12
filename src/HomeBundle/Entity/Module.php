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
    const STATUS_READY = 1;
    const STATUS_UPDATING = 2;
    const STATUS_UPDATE_NOT_COMMITTED = 3;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $code;

    /**
     * @var LogRecord[]
     *
     * @ORM\OneToMany(targetEntity="HomeBundle\Entity\LogRecord", mappedBy="module", cascade={"persist"})
     */
    private $logs;

    /**
     * @var Firmware
     *
     * @ORM\ManyToOne(targetEntity="HomeBundle\Entity\Firmware")
     */
    private $firmware;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $ip;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $status = self::STATUS_READY;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $lastPing = null;

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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Module
     */
    public function setCode($code)
    {
        $this->code = $code;
        
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '#' . (string) $this->id . '-' . (string) $this->name;
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
     * @param LogRecord $log
     *
     * @return $this
     */
    public function addLog($log)
    {
        $log->setModule($this);
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
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     *
     * @return $this
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastPing()
    {
        return $this->lastPing;
    }

    /**
     * @param \DateTime $lastPing
     */
    public function setLastPing($lastPing)
    {
        $this->lastPing = $lastPing;
    }
}