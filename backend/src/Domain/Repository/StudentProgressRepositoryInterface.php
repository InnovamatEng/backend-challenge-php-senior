<?php

namespace App\Domain\Repository;

use App\Domain\Model\Itinerary;
use App\Domain\Model\Student;
use App\Domain\Model\StudentProgress;

interface StudentProgressRepositoryInterface
{
    public function findByStudentAndItinerary(Student $student, Itinerary $itinerary): ?StudentProgress;

    public function save(StudentProgress $progress): void;
}
