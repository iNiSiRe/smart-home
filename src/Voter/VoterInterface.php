<?php


namespace Voter;


interface VoterInterface
{
    public function isActive() : bool;

    public function isForce() : bool;

    public function vote() : Vote;
}