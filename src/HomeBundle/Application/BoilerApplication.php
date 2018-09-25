<?php

namespace HomeBundle\Application;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use HomeBundle\Application\Boiler\Voter\DisableByTemperatureVoter;
use HomeBundle\Application\Boiler\Voter\EnableByTemperatureVoter;
use HomeBundle\Application\Boiler\Voter\InhabitantsCountVoter;
use HomeBundle\Application\Boiler\Voter\ManualModeVoter;
use HomeBundle\Application\Boiler\Voter\Votes;
use HomeBundle\Entity\BoilerUnit;
use HomeBundle\Model\Boiler;
use React\EventLoop\Timer\TimerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Voter\Manager\DecisionManager;
use Voter\Strategy\UntilFirstDisagreementDecisionStrategy;

class BoilerApplication
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var BoilerUnit
     */
    private $boilerUnit;

    /**
     * @var Boiler
     */
    private $boiler;

    /**
     * @var TimerInterface
     */
    private $timer;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;

    /**
     * @var DecisionManager
     */
    private $decisionManager;

    /**
     * BoilerApplication constructor.
     *
     * @param ContainerInterface $container
     * @param BoilerUnit         $boiler
     */
    public function __construct(ContainerInterface $container, BoilerUnit $boiler)
    {
        $this->container = $container;
        $this->boilerUnit = $boiler;
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
        $this->boiler = new Boiler($boiler, $this->container->get(ReactMqttClient::class));

        $this->decisionManager = new DecisionManager([
            new ManualModeVoter($boiler),
            new InhabitantsCountVoter($this->container->get('home.inhabitants_monitor')),
            new EnableByTemperatureVoter($boiler),
            new DisableByTemperatureVoter($boiler)
        ]);
    }

    public function isSatisfiedBySchedule()
    {
        $time = new \DateTime('now', new \DateTimeZone('Europe/Kiev'));
        $hours = $time->format('H');

        if ($hours >= 0 && $hours <=8) {
            return true;
        } elseif ($hours > 8 && $hours < 0) {
            return false;
        }

        return false;
    }

    public function loop()
    {
        $logger = $this->container->get('logger');

        $this->manager->refresh($this->boilerUnit);

        $logger->debug('BoilerApplication::loop -> begin');

        $current = $this->boilerUnit->isEnabled() ? Votes::VOTE_ENABLE : Votes::VOTE_DISABLE;
        $vote = $this->decisionManager->decide((new UntilFirstDisagreementDecisionStrategy($current))->setLogger($logger));

        $logger->debug(sprintf('Current vote: %s, decided vote: %s', $current, $vote->getValue()));

        switch ($vote->getValue()) {

           case Votes::VOTE_ENABLE:
               $this->boiler->enable();
               break;

           case Votes::VOTE_DISABLE:
               $this->boiler->disable();
               break;
        }

        $this->manager->flush($this->boilerUnit);
    }

    public function start()
    {
        $this->timer = $this->container->get('react.loop')->addPeriodicTimer(60, [$this, 'loop']);
    }
}