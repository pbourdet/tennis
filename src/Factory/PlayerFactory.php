<?php

namespace App\Factory;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PlayerFactory
 */
class PlayerFactory
{
    /**
     * @var PlayerRepository
     */
    private $playerRepo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PlayerFactory constructor.
     *
     * @param PlayerRepository       $playerRepo
     * @param EntityManagerInterface $em
     */
    public function __construct(PlayerRepository $playerRepo, EntityManagerInterface $em)
    {
        $this->playerRepo = $playerRepo;
        $this->em = $em;
    }

    /**
     * @param array $playerData
     *
     * @return Player
     *
     * @throws \Exception
     */
    public function create(array $playerData) : Player
    {
        $player = $this->playerRepo->findOneBy([
            'apiId' => $playerData['id'],
        ]);
        if (null === $player) {
            $player = new Player();
        }
        $player
            ->setApiId($playerData['id'])
            ->setName(str_replace(',', '', $playerData['name']))
            ->setNationality($playerData['nationality'])
            ->setCountryCode($playerData['country_code'])
            ->setAbbreviation($playerData['abbreviation'])
            ->setGender($playerData['gender'])
            ->setBirthDate(date_create_from_format('Y-m-d', $playerData['date_of_birth']))
            ->setProYear($playerData['pro_year'])
            ->setHandedness($playerData['handedness'])
            ->setHeight($playerData['height'])
            ->setWeight($playerData['weight'])
            ->setHighestSinglesRanking($playerData['highest_singles_ranking'])
            ->setHighestSinglesRankingDate(date_create_from_format('m.Y', $playerData['date_highest_singles_ranking']))
            ->setUpdatedAt(new \DateTime());
        $this->em->persist($player);
        $this->em->flush();

        return $player;
    }
}
