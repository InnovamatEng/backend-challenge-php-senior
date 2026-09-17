<?php

namespace App\Infrastructure\Http\Controller;

use App\Application\Service\AttemptService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AttemptsController extends AbstractController
{
    private const REQUIRED_FIELDS = ['student_id', 'activity_id', 'itinerary', 'score', 'passed', 'time_spent', 'completed_at'];

    public function __construct(
        private readonly AttemptService $attemptService,
    ) {
    }

    #[Route('/attempts', name: 'attempts_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Body must be a JSON object'], 400);
        }

        foreach (self::REQUIRED_FIELDS as $field) {
            if (!array_key_exists($field, $payload)) {
                return new JsonResponse(['error' => sprintf('Missing field "%s"', $field)], 400);
            }
        }

        $stats = $this->attemptService->record($payload);

        return new JsonResponse($stats->toArray(), 200);
    }
}
