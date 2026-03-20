<?php

namespace App\Infrastructure\Http\Controller;

use App\Application\Service\ActivityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Domain\Model\Student;
#[Route('/api')]
class ActivityController extends AbstractController
{
    public function __construct(
        private readonly ActivityService $activityService,
    ) {
    }

    #[Route('/getNextActivity', name: 'api_get_next_activity', methods: ['GET'])]
    public function getNextActivity(Request $request, #[CurrentUser] Student $student): JsonResponse
    {
        $itinerarySlug = $request->query->get('itinerary');
        if (!$itinerarySlug) {
            return new JsonResponse(['error' => 'itinerary parameter is required'], 400);
        }

        $studentId = $request->query->get('student_id', $student->getId());

        try {
            $activity = $this->activityService->getNextActivity((int) $studentId, $itinerarySlug);

            if ($activity === null) {
                return new JsonResponse(['message' => 'Itinerary completed', 'completed' => true], 200);
            }

            return new JsonResponse($activity, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/completeActivity', name: 'api_complete_activity', methods: ['POST'])]
    public function completeActivity(Request $request, #[CurrentUser] Student $student): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $activityIdentifier = $data['activity_id'] ?? null;
        $answers = $data['answers'] ?? '';
        $timeSpentMinutes = $data['time_spent'] ?? 0;
        $timeSpentSeconds = $timeSpentMinutes * 60;

        $studentId = $data['student_id'] ?? $student->getId();

        if (!$activityIdentifier) {
            return new JsonResponse(['error' => 'activity_id is required'], 400);
        }

        try {
            $result = $this->activityService->completeActivity(
                (int) $studentId,
                $activityIdentifier,
                $answers,
                $timeSpentSeconds
            );

            return new JsonResponse([
                'score' => $result['score'],
                'passed' => $result['passed'],
                'itinerary_completed' => $result['completed'],
                'next_activity' => $result['next_activity'],
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
