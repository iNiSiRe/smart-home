<?php


namespace Voter\Strategy;

use Monolog\Logger;
use Voter\Vote;
use Voter\VoterInterface;

class AnyAgreeDecisionStrategy implements DecisionStrategyInterface
{
    /**
     * @var mixed
     */
    private $vote;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param $vote
     */
    public function __construct($vote)
    {
        $this->vote = $vote;
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
        foreach ($voters as $voter) {

            if (!$voter->isActive()) {

                if ($this->logger) {
                    $this->logger->debug(sprintf('Voter "%s" is skipped', get_class($voter)));
                }

                continue;
            }

            $vote = $voter->vote();

            if ($vote->getValue() == $this->vote) {

                if ($this->logger) {
                    $this->logger->debug(sprintf('Voter "%s" is agree with vote "%s"', get_class($voter), $this->vote));
                }

                return new Vote(true);
            }

            if ($voter->isForce()) {
                break;
            }

        }

        return new Vote(false);
    }
}