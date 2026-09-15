<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Model\Itinerary;
use App\Domain\Repository\ItineraryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineItineraryRepository extends ServiceEntityRepository implements ItineraryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Itinerary::class);
    }

    public function findBySlug(string $slug): ?Itinerary
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function findById(int $id): ?Itinerary
    {
        return $this->find($id);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}
