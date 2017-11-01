<?php

namespace HomeBundle\Bridge;

use HomeBundle\Entity\Unit;

class BeamIntersectionSensorBridge
{
    /**
     * @var Unit
     */
    private $unit;

    /**
     * BeamIntersectionSensorBridge constructor.
     *
     * @param Unit $unit
     */
    public function __construct(Unit $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return mixed|null
     */
    public function getRoomFrom()
    {
        return $this->unit->getVariable('room_from');
    }

    /**
     * @return mixed|null
     */
    public function getRoomTo()
    {
        return $this->unit->getVariable('room_to');
    }
}