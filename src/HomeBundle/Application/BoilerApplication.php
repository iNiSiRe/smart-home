<?php

namespace HomeBundle\Application;

use BinSoul\Net\Mqtt\Client\React\ReactMqttClient;
use HomeBundle\Application\Boiler\Voter\DisableByTemperatureVoter;
use HomeBundle\Application\Boiler\Voter\EnableByTemperatureVoter;
use HomeBundle\Application\Boiler\Voter\InhabitantsCountVoter;
use HomeBundle\Application\Boiler\Voter\ManualModeVoter;
use HomeBundle\Application\Boiler\Voter\Votes;
use HomeBundle\Entity\BoilerUnit;
use HomeBundle\Entity\LogRecord;
use HomeBundle\Model\Boiler;
use React\EventLoop\Timer\TimerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Voter\Manager\DecisionManager;
use Voter\Strategy\AnyAgreeDecisionStrategy;
use Voter\Strategy\ComplexDecisionStrategy;
use Voter\Strategy\AllAgreeDecisionStrategy;
use Voter\Vote;

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
     * @param ContainerInterface            $container
     * @param BoilerUnit                    $boiler
     * @param InhabitantsMonitorApplication $inhabitantsMonitorApplication
     */
    public function __construct(ContainerInterface $container, BoilerUnit $boiler, InhabitantsMonitorApplication $inhabitantsMonitorApplication)
    {
        $this->container = $container;
        $this->boilerUnit = $boiler;
        $this->manager = $this->container->get('doctrine.orm.entity_manager');
        $this->boiler = new Boiler($boiler, $this->container->get(ReactMqttClient::class));

        $this->decisionManager = new DecisionManager([
            new ManualModeVoter($boiler),
            new InhabitantsCountVoter($inhabitantsMonitorApplication),
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

        $logger->debug('Boiler', ['enabled' => $this->boilerUnit->isEnabled()]);

        if ($this->boilerUnit->isEnabled()) {

            $vote = $this->decisionManager->decide((new AnyAgreeDecisionStrategy(Votes::VOTE_DISABLE))->setLogger($logger));

            $logger->debug('Vote for disable', ['vote' => $vote->getValue()]);

            if ($vote->getValue() == true) {
                $this->boiler->disable();
                $this->boilerUnit->getModule()->addLog(new LogRecord(json_encode([
                    'unit' => 'boiler',
                    'action' => 'disable',
                    'message' => $vote->getReason()
                ])));
                $this->manager->persist($this->boilerUnit);
            }

        } else {

            $vote = $this->decisionManager->decide((new AllAgreeDecisionStrategy(Votes::VOTE_ENABLE))->setLogger($logger));

            $logger->debug('Vote for enable', ['vote' => $vote->getValue()]);

            if ($vote->getValue() == true) {
                $this->boiler->enable();
                $this->boilerUnit->getModule()->addLog(new LogRecord(json_encode([
                    'unit' => 'boiler',
                    'action' => 'enable',
                    'message' => $vote->getReason()
                ])));
                $this->manager->persist($this->boilerUnit);
            }
        }

        $this->manager->flush();
    }

    public function start()
    {
        $this->timer = $this->container->get('react.loop')->addPeriodicTimer(60, [$this, 'loop']);
    }
}