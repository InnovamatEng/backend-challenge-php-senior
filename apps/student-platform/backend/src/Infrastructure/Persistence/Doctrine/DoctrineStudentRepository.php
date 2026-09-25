<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Model\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function findById(int $id): ?Student
    {
        return $this->find($id);
    }

    public function findByEmail(string $email): ?Student
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function save(Student $student): void
    {
        $this->getEntityManager()->persist($student);
        $this->getEntityManager()->flush();
    }
}
