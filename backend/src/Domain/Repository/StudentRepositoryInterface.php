<?php

namespace App\Domain\Repository;

use App\Domain\Model\Student;

interface StudentRepositoryInterface
{
    public function findById(int $id): ?Student;

    public function findByEmail(string $email): ?Student;

    /** @return Student[] */
    public function findAll(): array;

    public function save(Student $student): void;
}
