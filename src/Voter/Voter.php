<?php


namespace Voter;


abstract class Voter implements VoterInterface
{
    /**
     * By default voters are active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isForce(): bool
    {
        return false;
    }
}