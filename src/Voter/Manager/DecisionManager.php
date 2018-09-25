<?php

namespace Voter\Manager;

use Voter\Strategy\DecisionStrategyInterface;
use Voter\Vote;
use Voter\VoterInterface;

class DecisionManager
{
    /**
     * @var VoterInterface[]
     */
    private $voters;

    /**
     * @param VoterInterface[] $voters
     */
    public function __construct($voters)
    {
        $this->voters = $voters;
    }

    /**
     * @param DecisionStrategyInterface $strategy
     *
     * @return \Voter\Vote
     */
    public function decide(DecisionStrategyInterface $strategy) : Vote
    {
        return $strategy->decide($this->voters);
    }
}