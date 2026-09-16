<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Model\ActivityAttempt;
use App\Domain\Repository\ActivityAttemptRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineActivityAttemptRepository extends ServiceEntityRepository implements ActivityAttemptRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityAttempt::class);
    }

    public function add(ActivityAttempt $attempt): void
    {
        $this->getEntityManager()->persist($attempt);
    }
}
