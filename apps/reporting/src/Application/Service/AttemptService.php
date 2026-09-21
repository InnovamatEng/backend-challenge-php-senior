<?php

namespace App\Application\Service;

use App\Domain\Model\ActivityStats;
use App\Infrastructure\Persistence\Doctrine\DoctrineActivityStatsRepository;

class AttemptService
{
    public function __construct(
        private readonly DoctrineActivityStatsRepository $statsRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $attempt
     */
    public function record(array $attempt): ActivityStats
    {
        $this->statsRepository->recordAttempt(
            (string) $attempt['activity_id'],
            (string) $attempt['itinerary'],
            (float) $attempt['score'],
            (bool) $attempt['passed'],
            (int) $attempt['time_spent'],
            new \DateTimeImmutable((string) $attempt['completed_at']),
        );

        return $this->statsRepository->findByActivity((string) $attempt['activity_id']);
    }
}
