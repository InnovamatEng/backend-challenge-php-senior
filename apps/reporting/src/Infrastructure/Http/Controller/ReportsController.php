<?php

namespace App\Infrastructure\Http\Controller;

use App\Infrastructure\Persistence\Doctrine\DoctrineActivityStatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reports')]
class ReportsController extends AbstractController
{
    public function __construct(
        private readonly DoctrineActivityStatsRepository $statsRepository,
    ) {
    }

    #[Route('/activities', name: 'reports_activities', methods: ['GET'])]
    public function activities(): JsonResponse
    {
        return new JsonResponse(array_map(
            fn ($stats) => $stats->toArray(),
            $this->statsRepository->findAll()
        ));
    }
}
