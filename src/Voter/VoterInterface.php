<?php


namespace Voter;


interface VoterInterface
{
    public function isActive() : bool;

    public function vote() : Vote;
}