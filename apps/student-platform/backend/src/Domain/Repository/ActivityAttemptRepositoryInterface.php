<?php

namespace App\Domain\Repository;

use App\Domain\Model\ActivityAttempt;

interface ActivityAttemptRepositoryInterface
{
    /**
     * Schedules the attempt for insertion. It is written on the next flush.
     */
    public function add(ActivityAttempt $attempt): void;
}
