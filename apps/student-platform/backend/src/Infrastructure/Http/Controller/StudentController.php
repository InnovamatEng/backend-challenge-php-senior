<?php

namespace App\Infrastructure\Http\Controller;

use App\Infrastructure\Persistence\Doctrine\DoctrineStudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class StudentController extends AbstractController
{
    public function __construct(
        private readonly DoctrineStudentRepository $studentRepository,
    ) {
    }

    #[Route('/students', name: 'api_list_students', methods: ['GET'])]
    public function listStudents(): JsonResponse
    {
        $students = $this->studentRepository->findAll();

        $data = array_map(fn ($s) => [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'email' => $s->getEmail(),
        ], $students);

        return new JsonResponse($data);
    }
}
