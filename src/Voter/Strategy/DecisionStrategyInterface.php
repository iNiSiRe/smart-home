<?php

namespace Voter\Strategy;

use Voter\Vote;

interface DecisionStrategyInterface
{
    public function decide($voters) : Vote;
}