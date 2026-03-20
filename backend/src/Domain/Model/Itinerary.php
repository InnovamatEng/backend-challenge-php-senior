<?php

namespace App\Domain\Model;

use App\Infrastructure\Persistence\Doctrine\DoctrineItineraryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DoctrineItineraryRepository::class)]
#[ORM\Table(name: 'itineraries')]
class Itinerary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $slug;

    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'itinerary', cascade: ['persist'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getActivities(): Collection
    {
        return $this->activities;
    }
}
