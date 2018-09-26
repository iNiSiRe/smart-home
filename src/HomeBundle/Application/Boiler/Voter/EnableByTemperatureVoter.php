<?php


namespace HomeBundle\Application\Boiler\Voter;


use HomeBundle\Entity\BoilerUnit;
use Voter\Vote;
use Voter\Voter;

class EnableByTemperatureVoter extends Voter
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
        return $this->boilerUnit->isEnabled() === false;
    }

    /**
     * @return Vote
     */
    public function vote(): Vote
    {
        $sensors = $this->boilerUnit->getSensors();
        $sensor = $sensors[0];

        // Find sensor with lowest temp
        for ($i = 1; $i < count($sensors); $i++) {
            if ($sensors[$i]->getTemperature() < $sensor->getTemperature()) {
                $sensor = $sensors[$i];
            }
        }

        if ($sensor->getTemperature() <= ($this->boilerUnit->getTemperature() - 1)) {
            return new Vote(Votes::VOTE_ENABLE);
        }

        return new Vote(Votes::VOTE_DISABLE);
    }
}