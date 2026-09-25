<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Model\Activity;
use App\Domain\Model\Itinerary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function findByIdentifier(string $identifier): ?Activity
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }

    public function findByIdAndItinerary(int $id, Itinerary $itinerary): ?Activity
    {
        return $this->findOneBy(['id' => $id, 'itinerary' => $itinerary]);
    }
    public function findAllByItinerary(Itinerary $itinerary): array
    {
        $activities = [];
        foreach ($itinerary->getActivities() as $activity) {
            $activities[] = $this->find($activity->getId());
        }
        return $activities;
    }

    public function findAllByDifficulty(Itinerary $itinerary, int $difficulty): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.itinerary = :itinerary')
            ->andWhere('a.difficulty = :difficulty')
            ->setParameter('itinerary', $itinerary)
            ->setParameter('difficulty', $difficulty)
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Activity $activity): void
    {
        $this->getEntityManager()->persist($activity);
        $this->getEntityManager()->flush();
    }
}
