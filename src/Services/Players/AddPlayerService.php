<?php

namespace App\Services\Players;

use App\Entity\Player;
use App\Factory\PlayerDoublesRankingFactory;
use App\Factory\PlayerFactory;
use App\Factory\PlayerSinglesRankingFactory;
use App\Factory\PlayerStatisticsFactory;
use App\Services\API\APICall;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * Class AddPlayerService
 */
class AddPlayerService
{
    /**
     * @var APICall
     */
    private $apiCall;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PlayerFactory
     */
    private $playerFactory;

    /**
     * @var PlayerSinglesRankingFactory
     */
    private $singlesRankingFactory;

    /**
     * @var PlayerDoublesRankingFactory
     */
    private $doublesRankingFactory;

    /**
     * @var PlayerStatisticsFactory
     */
    private $statisticsFactory;

    /**
     * AddPlayer constructor.
     *
     * @param APICall                     $apiCall
     * @param EntityManagerInterface      $em
     * @param PlayerFactory               $playerFactory
     * @param PlayerSinglesRankingFactory $singlesRankingFactory
     * @param PlayerDoublesRankingFactory $doublesRankingFactory
     * @param PlayerStatisticsFactory     $statisticsFactory
     */
    public function __construct(APICall $apiCall, EntityManagerInterface $em, PlayerFactory $playerFactory, PlayerSinglesRankingFactory $singlesRankingFactory, PlayerDoublesRankingFactory $doublesRankingFactory, PlayerStatisticsFactory $statisticsFactory)
    {
        $this->apiCall = $apiCall;
        $this->em = $em;
        $this->playerFactory = $playerFactory;
        $this->singlesRankingFactory = $singlesRankingFactory;
        $this->doublesRankingFactory = $doublesRankingFactory;
        $this->statisticsFactory = $statisticsFactory;
    }

    /**
     * @param $apiIdInt
     *
     * @return Player
     *
     * @throws ExceptionInterface|\Exception
     */
    public function addPlayer($apiIdInt) : Player
    {
        $url = 'https://api.sportradar.com/tennis-t2/fr/players/sr:competitor:'.$apiIdInt.'/profile.json?api_key=';
        $playerArray = $this->apiCall->sportradarCall($url);
        $player = $this->playerFactory->create($playerArray['player']);
        $singlesRanking = $this->singlesRankingFactory->create($player, $playerArray['rankings']);
        $doublesRanking = $this->doublesRankingFactory->create($player, $playerArray['rankings']);
        $statistics = $this->statisticsFactory->create($player, $playerArray['statistics']);
        $this->em->persist($player);
        if (null !== $singlesRanking) {
            $this->em->persist($singlesRanking);
        }
        if (null !== $doublesRanking) {
            $this->em->persist($doublesRanking);
        }
        foreach ($statistics as $stats) {
            $this->em->persist($stats);
        }
        $this->em->flush();

        return $player;
    }
}
