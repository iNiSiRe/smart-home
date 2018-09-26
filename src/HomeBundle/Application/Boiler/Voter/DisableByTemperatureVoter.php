<?php

namespace HomeBundle\Application\Boiler\Voter;

use HomeBundle\Entity\BoilerUnit;
use Voter\Vote;
use Voter\Voter;

class DisableByTemperatureVoter extends Voter
{
    /**
     * @var BoilerUnit
     */
    private $boilerUnit;

    /**
     * @param BoilerUnit $boilerUnit
     */
    public function __construct(BoilerUnit $boilerUnit)
    {
        $this->boilerUnit = $boilerUnit;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->boilerUnit->isEnabled() === true;
    }

    /**
     * @return Vote
     */
    public function vote(): Vote
    {
        $sensors = $this->boilerUnit->getSensors();
        $sensor = $sensors[0];

        // Find sensor with max temp
        for ($i = 1; $i < count($sensors); $i++) {
            if ($sensors[$i]->getTemperature() > $sensor->getTemperature()) {
                $sensor = $sensors[$i];
            }
        }

        if ($sensor->getTemperature() > $this->boilerUnit->getTemperature()) {
            return new Vote(Votes::VOTE_DISABLE);
        } else {
            return new Vote(Votes::VOTE_ENABLE);
        }
    }
}