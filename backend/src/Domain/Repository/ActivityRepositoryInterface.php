<?php

namespace App\Domain\Repository;

use App\Domain\Model\Activity;
use App\Domain\Model\Itinerary;

interface ActivityRepositoryInterface
{
    public function findByIdentifier(string $identifier): ?Activity;

    public function findByIdAndItinerary(int $id, Itinerary $itinerary): ?Activity;

    /** @return array */
    public function findAllByItinerary(Itinerary $itinerary): array;

    /** @return array */
    public function findAllByDifficulty(Itinerary $itinerary, int $difficulty): array;

    public function save(Activity $activity): void;
}
