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
        $sensor = $this->boilerUnit->getSensors()[0];

        if ($sensor->getTemperature() <= ($this->boilerUnit->getTemperature() - 1)) {
            return new Vote(Votes::VOTE_ENABLE);
        }

        return new Vote(Votes::VOTE_DISABLE);
    }
}