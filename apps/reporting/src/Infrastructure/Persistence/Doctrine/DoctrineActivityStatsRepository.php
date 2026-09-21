<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Model\ActivityStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineActivityStatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityStats::class);
    }

    public function recordAttempt(
        string $activityId,
        string $itinerary,
        float $score,
        bool $passed,
        int $timeSpent,
        \DateTimeImmutable $completedAt,
    ): void {
        $this->getEntityManager()->getConnection()->executeStatement(
            'INSERT INTO activity_stats
                (activity_id, itinerary, attempts_count, passed_count, score_sum, average_score, time_spent_sum, last_attempt_at)
             VALUES
                (:activityId, :itinerary, 1, :passed, :score, :score, :timeSpent, :completedAt)
             ON DUPLICATE KEY UPDATE
                attempts_count = attempts_count + 1,
                passed_count = passed_count + :passed,
                score_sum = score_sum + :score,
                average_score = score_sum / attempts_count,
                time_spent_sum = time_spent_sum + :timeSpent,
                last_attempt_at = GREATEST(last_attempt_at, :completedAt)',
            [
                'activityId' => $activityId,
                'itinerary' => $itinerary,
                'passed' => $passed ? 1 : 0,
                'score' => $score,
                'timeSpent' => $timeSpent,
                'completedAt' => $completedAt->format('Y-m-d H:i:s'),
            ]
        );

        $this->getEntityManager()->clear(ActivityStats::class);
    }

    public function findByActivity(string $activityId): ?ActivityStats
    {
        return $this->find($activityId);
    }

    public function findAll(): array
    {
        return $this->findBy([], ['itinerary' => 'ASC', 'activityId' => 'ASC']);
    }
}
