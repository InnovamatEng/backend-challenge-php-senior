<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Model\Itinerary;
use App\Domain\Model\Student;
use App\Domain\Model\StudentProgress;
use App\Domain\Repository\StudentProgressRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineStudentProgressRepository extends ServiceEntityRepository implements StudentProgressRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentProgress::class);
    }
    public function findByStudentAndItinerary(Student $student, Itinerary $itinerary): ?StudentProgress
    {
        $studentId = $student->getId();
        $itineraryId = $itinerary->getId();

        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM student_progress WHERE student_id = $studentId AND itinerary_id = $itineraryId LIMIT 1";

        $result = $conn->executeQuery($sql)->fetchOne();

        if (!$result) {
            return null;
        }

        return $this->find((int) $result);
    }

    public function save(StudentProgress $progress): void
    {
        $this->getEntityManager()->persist($progress);
        $this->getEntityManager()->flush();
    }
}
