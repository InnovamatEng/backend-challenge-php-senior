<?php

namespace App\Domain\Model;

use App\Infrastructure\Persistence\Doctrine\DoctrineActivityAttemptRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineActivityAttemptRepository::class)]
#[ORM\Table(name: 'activity_attempts')]
class ActivityAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $studentId;

    #[ORM\Column(type: 'string', length: 50)]
    private string $activityIdentifier;

    #[ORM\Column(type: 'string', length: 50)]
    private string $itinerarySlug;

    #[ORM\Column(type: 'float')]
    private float $score;

    #[ORM\Column(type: 'integer')]
    private int $timeSpent;

    #[ORM\Column(type: 'string', length: 500)]
    private string $answers;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $completedAt;

    public function __construct()
    {
        $this->completedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function setStudentId(int $studentId): void
    {
        $this->studentId = $studentId;
    }

    public function getActivityIdentifier(): string
    {
        return $this->activityIdentifier;
    }

    public function setActivityIdentifier(string $activityIdentifier): void
    {
        $this->activityIdentifier = $activityIdentifier;
    }

    public function getItinerarySlug(): string
    {
        return $this->itinerarySlug;
    }

    public function setItinerarySlug(string $itinerarySlug): void
    {
        $this->itinerarySlug = $itinerarySlug;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function setScore(float $score): void
    {
        $this->score = $score;
    }

    public function getTimeSpent(): int
    {
        return $this->timeSpent;
    }

    public function setTimeSpent(int $timeSpent): void
    {
        $this->timeSpent = $timeSpent;
    }

    public function getAnswers(): string
    {
        return $this->answers;
    }

    public function setAnswers(string $answers): void
    {
        $this->answers = $answers;
    }

    public function getCompletedAt(): \DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(\DateTimeImmutable $completedAt): void
    {
        $this->completedAt = $completedAt;
    }
}
