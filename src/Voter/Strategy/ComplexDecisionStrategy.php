<?php


namespace Voter\Strategy;

use Monolog\Logger;
use Voter\Vote;
use Voter\VoterInterface;

class ComplexDecisionStrategy implements DecisionStrategyInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $votes;

    /**
     * @param array $availableVotes
     */
    public function __construct($availableVotes)
    {
        $this->votes = $availableVotes;
    }

    /**
     * @param Logger $logger
     *
     * @return $this
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param VoterInterface[] $voters
     *
     * @return \Voter\Vote
     */
    public function decide($voters) : Vote
    {
        $votes = [];
        foreach ($this->votes as $vote) {
            $votes[$vote] = 0;
        }

        foreach ($voters as $voter) {

            if (!$voter->isActive()) {

                if ($this->logger) {
                    $this->logger->debug(sprintf('Voter "%s" is skipped', get_class($voter)));
                }

                continue;
            }

            $vote = $voter->vote();

            if ($this->logger) {
                $this->logger->debug(sprintf('Voter "%s" is vote for "%s"', get_class($voter), $vote->getValue()));
            }

            $votes[$vote->getValue()]++;

        }

        return new Vote($votes);
    }
}