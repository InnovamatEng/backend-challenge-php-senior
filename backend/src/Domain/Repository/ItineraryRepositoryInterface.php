<?php

namespace App\Domain\Repository;

use App\Domain\Model\Itinerary;

interface ItineraryRepositoryInterface
{
    public function findBySlug(string $slug): ?Itinerary;

    public function findById(int $id): ?Itinerary;

    /** @return Itinerary[] */
    public function findAll(): array;
}
