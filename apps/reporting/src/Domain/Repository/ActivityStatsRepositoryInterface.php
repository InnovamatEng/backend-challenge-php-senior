<?php

namespace App\Domain\Repository;

use App\Domain\Model\ActivityStats;

interface ActivityStatsRepositoryInterface
{
    /**
     * Folds one attempt into the stats of its activity.
     */
    public function recordAttempt(
        string $activityId,
        string $itinerary,
        float $score,
        bool $passed,
        int $timeSpent,
        \DateTimeImmutable $completedAt,
    ): void;

    public function findByActivity(string $activityId): ?ActivityStats;

    /** @return ActivityStats[] */
    public function findAll(): array;
}
