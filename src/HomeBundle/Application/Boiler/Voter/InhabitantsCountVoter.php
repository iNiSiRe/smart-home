<?php


namespace HomeBundle\Application\Boiler\Voter;

use HomeBundle\Application\InhabitantsMonitorApplication;
use Voter\Vote;
use Voter\Voter;

class InhabitantsCountVoter extends Voter
{
    /**
     * @var InhabitantsMonitorApplication
     */
    private $inhabitantsMonitor;

    /**
     * @param InhabitantsMonitorApplication $inhabitantsMonitor
     */
    public function __construct(InhabitantsMonitorApplication $inhabitantsMonitor)
    {
        $this->inhabitantsMonitor = $inhabitantsMonitor;
    }

    /**
     * @return Vote
     */
    public function vote(): Vote
    {
        $inhabitants = $this->inhabitantsMonitor->getInhabitants();
        $value = count($inhabitants) > 0 ? Votes::VOTE_ENABLE : Votes::VOTE_DISABLE;

        return new Vote($value, sprintf('%s::%s -> %s', __CLASS__, __METHOD__, count($inhabitants)));
    }
}