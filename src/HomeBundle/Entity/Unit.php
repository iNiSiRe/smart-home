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
 * @ORM\DiscriminatorMap({1 = "Unit", 5 = "BoilerUnit"})
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
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $config;

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
     * @return Unit
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
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
     * @return Unit
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
