<?php

namespace App\Domain\Model;

use App\Infrastructure\Persistence\Doctrine\DoctrineStudentProgressRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineStudentProgressRepository::class)]
#[ORM\Table(name: 'student_progress')]
class StudentProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: Itinerary::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Itinerary $itinerary;

    #[ORM\ManyToOne(targetEntity: Activity::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Activity $currentActivity = null;

    #[ORM\Column(type: 'boolean')]
    private bool $completed = false;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $lastScore = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    public function __construct()
    {
        $this->startedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function setStudent(Student $student): void
    {
        $this->student = $student;
    }

    public function getItinerary(): Itinerary
    {
        return $this->itinerary;
    }

    public function setItinerary(Itinerary $itinerary): void
    {
        $this->itinerary = $itinerary;
    }

    public function getCurrentActivity(): ?Activity
    {
        return $this->currentActivity;
    }

    public function setCurrentActivity(?Activity $activity): void
    {
        $this->currentActivity = $activity;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getLastScore(): ?float
    {
        return $this->lastScore;
    }

    public function setLastScore(?float $lastScore): void
    {
        $this->lastScore = $lastScore;
    }

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): void
    {
        $this->completedAt = $completedAt;
    }
}
