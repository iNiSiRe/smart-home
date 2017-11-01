<?php

namespace HomeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table(name="rooms")
 * @ORM\Entity()
 */
class Room
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Module[]
     *
     * @ORM\OneToMany(targetEntity="HomeBundle\Entity\Unit", mappedBy="room")
     */
    private $units;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $variables;

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
     * @return Room
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
    }

    /**
     * Add unit
     *
     * @param Unit $unit
     *
     * @return Room
     */
    public function addUnit(Unit $unit)
    {
        $this->units[] = $unit;

        return $this;
    }

    /**
     * Remove unit
     *
     * @param Unit $sensor
     */
    public function removeUnit(Unit $sensor)
    {
        $this->units->removeElement($sensor);
    }

    /**
     * Get unit
     *
     * @return Unit[]|ArrayCollection
     */
    public function getUnits()
    {
        return $this->units;
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
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     *
     * @return Room
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function getVariable($name)
    {
        return $this->variables[$name] ?? null;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;

        return $this;
    }
}
