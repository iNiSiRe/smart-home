<?php

namespace HomeBundle\Application\Boiler\Voter;

use HomeBundle\Entity\BoilerUnit;
use Voter\Vote;
use Voter\Voter;

class DisableByTemperatureVoter extends Voter
{
    /**
     * @var BoilerUnit
     */
    private $boilerUnit;

    /**
     * @var bool
     */
    private $smartNight;

    /**
     * @param BoilerUnit $boilerUnit
     * @param bool       $smartNight
     */
    public function __construct(BoilerUnit $boilerUnit, $smartNight = true)
    {
        $this->boilerUnit = $boilerUnit;
        $this->smartNight = $smartNight;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->boilerUnit->isEnabled() === true;
    }

    /**
     * @return Vote
     */
    public function vote(): Vote
    {
        $sensors = $this->boilerUnit->getSensors();
        $sensor = $sensors[0];

        // Find sensor with max temp
        for ($i = 1; $i < count($sensors); $i++) {
            if ($sensors[$i]->getTemperature() > $sensor->getTemperature()) {
                $sensor = $sensors[$i];
            }
        }

        // Smart night mode
        $now = new \DateTime('now', new \DateTimeZone('Europe/Kiev'));
        $hours = (int) $now->format('G');

        if ($this->smartNight && ($hours >= 23 || $hours <= 6)) {
            $temperature = 19.5;
        } else {
            $temperature = $this->boilerUnit->getTemperature();
        }

        if ($sensor->getTemperature() > $temperature) {
            return new Vote(Votes::VOTE_DISABLE);
        } else {
            return new Vote(Votes::VOTE_ENABLE);
        }
    }
}