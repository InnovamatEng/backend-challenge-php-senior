<?php

namespace App\Application\Service;

use App\Domain\Model\Activity;
use App\Domain\Model\ActivityAttempt;
use App\Domain\Model\StudentProgress;
use App\Domain\Repository\ActivityAttemptRepositoryInterface;
use App\Domain\Repository\ActivityRepositoryInterface;
use App\Domain\Repository\ItineraryRepositoryInterface;
use App\Domain\Repository\StudentProgressRepositoryInterface;
use App\Domain\Repository\StudentRepositoryInterface;
use App\Infrastructure\Reporting\ReportingClient;
use Doctrine\ORM\EntityManagerInterface;
class ActivityService
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ItineraryRepositoryInterface $itineraryRepository,
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly StudentProgressRepositoryInterface $progressRepository,
        private readonly ActivityAttemptRepositoryInterface $attemptRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ReportingClient $reportingClient,
    ) {
    }

    public function getNextActivity(int $studentId, string $itinerarySlug): ?array
    {
        $student = $this->studentRepository->findById($studentId);
        if (!$student) {
            throw new \RuntimeException('Student not found');
        }

        $itinerary = $this->itineraryRepository->findBySlug($itinerarySlug);
        if (!$itinerary) {
            throw new \RuntimeException('Itinerary not found');
        }

        $progress = $this->progressRepository->findByStudentAndItinerary($student, $itinerary);

        if (!$progress) {
            // Student hasn't started the itinerary - return first activity
            $activities = $this->activityRepository->findAllByItinerary($itinerary);
            if (empty($activities)) {
                throw new \RuntimeException('Itinerary has no activities');
            }

            $firstActivity = $activities[0];
            $newProgress = new StudentProgress();
            $newProgress->setStudent($student);
            $newProgress->setItinerary($itinerary);
            $newProgress->setCurrentActivity($firstActivity);
            $this->progressRepository->save($newProgress);

            return $this->formatActivity($firstActivity);
        }

        if ($progress->isCompleted()) {
            return null;
        }

        return $this->formatActivity($progress->getCurrentActivity());
    }

    public function completeActivity(int $studentId, string $activityIdentifier, string $answers, int $timeSpent): array
    {
        $student = $this->studentRepository->findById($studentId);
        if (!$student) {
            throw new \RuntimeException('Student not found');
        }

        $activity = $this->activityRepository->findByIdentifier($activityIdentifier);
        if (!$activity) {
            throw new \RuntimeException('Activity not found');
        }

        $itinerary = $activity->getItinerary();
        $progress = $this->progressRepository->findByStudentAndItinerary($student, $itinerary);

        if (!$progress) {
            throw new \RuntimeException('Student has not started this itinerary');
        }

        if ($progress->isCompleted()) {
            throw new \RuntimeException('Itinerary already completed');
        }

        $given = explode('_', $answers);
        $expected = explode('_', $activity->getSolution());
        $correct = 0;
        for ($i = 0; $i < count($expected); $i++) {
            if (isset($given[$i]) && $given[$i] == $expected[$i]) {
                $correct++;
            }
        }
        $score = $correct / count($expected);

        $progress->setLastScore($score);

        if ($score >= 0.75) {
            // Find next activity
            $allActivities = $this->activityRepository->findAllByItinerary($itinerary);
            $currentPosition = $activity->getPosition();
            $nextActivity = null;

            foreach ($allActivities as $act) {
                if ($act->getPosition() > $currentPosition) {
                    $nextActivity = $act;
                    break;
                }
            }

            if ($nextActivity === null) {
                // Last activity completed successfully
                $progress->setCompleted(true);
                $progress->setCurrentActivity(null);
                $progress->setCompletedAt(new \DateTimeImmutable());
            } else {
                $progress->setCurrentActivity($nextActivity);
            }
        }

        $attempt = new ActivityAttempt();
        $attempt->setStudentId($student->getId());
        $attempt->setActivityIdentifier($activity->getIdentifier());
        $attempt->setItinerarySlug($itinerary->getSlug());
        $attempt->setScore($score);
        $attempt->setTimeSpent($timeSpent);
        $attempt->setAnswers($answers);
        $this->attemptRepository->add($attempt);

        $this->reportingClient->registerAttempt([
            'student_id' => $attempt->getStudentId(),
            'activity_id' => $attempt->getActivityIdentifier(),
            'itinerary' => $attempt->getItinerarySlug(),
            'score' => $attempt->getScore(),
            'passed' => $score >= 0.75,
            'time_spent' => $attempt->getTimeSpent(),
            'completed_at' => $attempt->getCompletedAt()->format(\DateTimeInterface::ATOM),
        ]);

        $this->entityManager->flush();

        return [
            'score' => $score,
            'passed' => $score >= 0.75,
            'completed' => $progress->isCompleted(),
            'next_activity' => $progress->getCurrentActivity()
                ? $this->formatActivity($progress->getCurrentActivity())
                : null,
        ];
    }

    private function formatActivity(Activity $activity): array
    {
        return [
            'id' => $activity->getId(),
            'identifier' => $activity->getIdentifier(),
            'name' => $activity->getName(),
            'difficulty' => $activity->getDifficulty(),
            'estimated_time' => $activity->getEstimatedTime(),
            'exercises_count' => count(explode('_', $activity->getSolution())),
        ];
    }
}
