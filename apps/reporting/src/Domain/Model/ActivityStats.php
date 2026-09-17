<?php

namespace App\Domain\Model;

use App\Infrastructure\Persistence\Doctrine\DoctrineActivityStatsRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineActivityStatsRepository::class)]
#[ORM\Table(name: 'activity_stats')]
class ActivityStats
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 50)]
    private string $activityId;

    #[ORM\Column(type: 'string', length: 50)]
    private string $itinerary;

    #[ORM\Column(type: 'integer')]
    private int $attemptsCount = 0;

    #[ORM\Column(type: 'integer')]
    private int $passedCount = 0;

    #[ORM\Column(type: 'float')]
    private float $scoreSum = 0.0;

    #[ORM\Column(type: 'float')]
    private float $averageScore = 0.0;

    #[ORM\Column(type: 'integer')]
    private int $timeSpentSum = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $lastAttemptAt;

    public function getActivityId(): string
    {
        return $this->activityId;
    }

    public function getItinerary(): string
    {
        return $this->itinerary;
    }

    public function getAttemptsCount(): int
    {
        return $this->attemptsCount;
    }

    public function getPassedCount(): int
    {
        return $this->passedCount;
    }

    public function getScoreSum(): float
    {
        return $this->scoreSum;
    }

    public function getAverageScore(): float
    {
        return $this->averageScore;
    }

    public function getTimeSpentSum(): int
    {
        return $this->timeSpentSum;
    }

    public function getLastAttemptAt(): \DateTimeImmutable
    {
        return $this->lastAttemptAt;
    }

    public function toArray(): array
    {
        return [
            'activity_id' => $this->activityId,
            'itinerary' => $this->itinerary,
            'attempts_count' => $this->attemptsCount,
            'passed_count' => $this->passedCount,
            'average_score' => round($this->averageScore, 4),
            'average_time_spent' => $this->attemptsCount > 0 ? (int) round($this->timeSpentSum / $this->attemptsCount) : 0,
            'last_attempt_at' => $this->lastAttemptAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
