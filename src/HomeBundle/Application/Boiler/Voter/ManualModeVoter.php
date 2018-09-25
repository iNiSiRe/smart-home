<?php

namespace HomeBundle\Application\Boiler\Voter;

use HomeBundle\Entity\BoilerUnit;
use Voter\Vote;
use Voter\VoterInterface;

class ManualModeVoter implements VoterInterface
{
    /**
     * @var BoilerUnit
     */
    private $boiler;

    /**
     * @param BoilerUnit $boiler
     */
    public function __construct(BoilerUnit $boiler)
    {
        $this->boiler = $boiler;
    }

    /**
     * @return Vote
     */
    public function vote(): Vote
    {
        $value = $this->boiler->isEnabled() ? Votes::VOTE_ENABLE : Votes::VOTE_DISABLE;

        return new Vote($value);
    }

    public function isActive(): bool
    {
        return $this->boiler->getManual()->isEnabled();
    }
}