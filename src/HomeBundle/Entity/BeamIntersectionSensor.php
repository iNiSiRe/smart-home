<?php

namespace HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class BeamIntersectionSensor extends Unit
{
    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="HomeBundle\Entity\Room")
     */
    private $roomFrom;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="HomeBundle\Entity\Room")
     */
    private $roomTo;

    /**
     * @var SwitchUnit
     *
     * @ORM\ManyToOne(targetEntity="HomeBundle\Entity\SwitchUnit")
     */
    private $light;

    /**
     * @return Room
     */
    public function getRoomFrom()
    {
        return $this->roomFrom;
    }

    /**
     * @param Room $roomFrom
     *
     * @return BeamIntersectionSensor
     */
    public function setRoomFrom($roomFrom)
    {
        $this->roomFrom = $roomFrom;

        return $this;
    }

    /**
     * @return Room
     */
    public function getRoomTo()
    {
        return $this->roomTo;
    }

    /**
     * @param Room $roomTo
     *
     * @return BeamIntersectionSensor
     */
    public function setRoomTo($roomTo)
    {
        $this->roomTo = $roomTo;

        return $this;
    }

    /**
     * @return SwitchUnit
     */
    public function getLight()
    {
        return $this->light;
    }

    /**
     * @param SwitchUnit $light
     *
     * @return BeamIntersectionSensor
     */
    public function setLight($light)
    {
        $this->light = $light;

        return $this;
    }
}