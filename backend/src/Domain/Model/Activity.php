<?php

namespace App\Domain\Model;

use App\Infrastructure\Persistence\Doctrine\DoctrineActivityRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineActivityRepository::class)]
#[ORM\Table(name: 'activities')]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $identifier;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $difficulty;

    #[ORM\Column(type: 'integer')]
    private int $position;

    #[ORM\Column(type: 'integer')]
    private int $estimatedTime;

    #[ORM\Column(type: 'string', length: 500)]
    private string $solution;

    #[ORM\ManyToOne(targetEntity: Itinerary::class, inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private Itinerary $itinerary;

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDifficulty(): int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): void
    {
        $this->difficulty = $difficulty;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getEstimatedTime(): int
    {
        return $this->estimatedTime;
    }

    public function setEstimatedTime(int $estimatedTime): void
    {
        $this->estimatedTime = $estimatedTime;
    }

    public function getSolution(): string
    {
        return $this->solution;
    }

    public function setSolution(string $solution): void
    {
        $this->solution = $solution;
    }

    public function getItinerary(): Itinerary
    {
        return $this->itinerary;
    }

    public function setItinerary(Itinerary $itinerary): void
    {
        $this->itinerary = $itinerary;
    }
}
