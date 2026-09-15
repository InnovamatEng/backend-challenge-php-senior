<?php

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\ActivityService;
use App\Domain\Model\Activity;
use App\Domain\Model\Itinerary;
use App\Domain\Model\Student;
use App\Domain\Model\StudentProgress;
use App\Domain\Repository\ActivityRepositoryInterface;
use App\Domain\Repository\ItineraryRepositoryInterface;
use App\Domain\Repository\StudentProgressRepositoryInterface;
use App\Domain\Repository\StudentRepositoryInterface;
use App\Infrastructure\Reporting\ReportingClient;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
class ActivityServiceTest extends TestCase
{
    private ActivityRepositoryInterface $activityRepository;
    private ItineraryRepositoryInterface $itineraryRepository;
    private StudentRepositoryInterface $studentRepository;
    private StudentProgressRepositoryInterface $progressRepository;
    private EntityManagerInterface $entityManager;
    private ReportingClient $reportingClient;
    private ActivityService $service;

    protected function setUp(): void
    {
        $this->activityRepository = $this->createMock(ActivityRepositoryInterface::class);
        $this->itineraryRepository = $this->createMock(ItineraryRepositoryInterface::class);
        $this->studentRepository = $this->createMock(StudentRepositoryInterface::class);
        $this->progressRepository = $this->createMock(StudentProgressRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->reportingClient = $this->createMock(ReportingClient::class);

        $this->service = new ActivityService(
            $this->activityRepository,
            $this->itineraryRepository,
            $this->studentRepository,
            $this->progressRepository,
            $this->entityManager,
            $this->reportingClient,
        );
    }

    public function test_get_next_activity_returns_first_activity_when_no_progress(): void
    {
        $student = $this->createMock(Student::class);
        $student->method('getId')->willReturn(1);

        $itinerary = $this->createMock(Itinerary::class);

        $activity = $this->createMock(Activity::class);
        $activity->method('getId')->willReturn(1);
        $activity->method('getIdentifier')->willReturn('A1');
        $activity->method('getName')->willReturn('Activity 1');
        $activity->method('getDifficulty')->willReturn(1);
        $activity->method('getEstimatedTime')->willReturn(120);
        $activity->method('getSolution')->willReturn('1_0_2');

        $this->studentRepository->method('findById')->with(1)->willReturn($student);
        $this->itineraryRepository->method('findBySlug')->with('additions')->willReturn($itinerary);
        $this->progressRepository->method('findByStudentAndItinerary')->willReturn(null);
        $this->activityRepository->method('findAllByItinerary')->willReturn([$activity]);
        $this->progressRepository->expects($this->once())->method('save');

        $result = $this->service->getNextActivity(1, 'additions');

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('identifier', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('A1', $result['identifier']);
    }

    public function test_complete_activity_with_perfect_score_advances_to_next(): void
    {
        $student = $this->createMock(Student::class);
        $student->method('getId')->willReturn(1);

        $itinerary = $this->createMock(Itinerary::class);

        $currentActivity = $this->createMock(Activity::class);
        $currentActivity->method('getIdentifier')->willReturn('A1');
        $currentActivity->method('getSolution')->willReturn('1_0_2');
        $currentActivity->method('getPosition')->willReturn(1);
        $currentActivity->method('getEstimatedTime')->willReturn(120);
        $currentActivity->method('getItinerary')->willReturn($itinerary);

        $nextActivity = $this->createMock(Activity::class);
        $nextActivity->method('getId')->willReturn(2);
        $nextActivity->method('getIdentifier')->willReturn('A2');
        $nextActivity->method('getName')->willReturn('Activity 2');
        $nextActivity->method('getDifficulty')->willReturn(1);
        $nextActivity->method('getEstimatedTime')->willReturn(60);
        $nextActivity->method('getSolution')->willReturn('-2_40_56');
        $nextActivity->method('getPosition')->willReturn(2);

        $progress = $this->createMock(StudentProgress::class);
        $progress->method('isCompleted')->willReturn(false);

        $this->studentRepository->method('findById')->willReturn($student);
        $this->activityRepository->method('findByIdentifier')->willReturn($currentActivity);
        $this->progressRepository->method('findByStudentAndItinerary')->willReturn($progress);
        $this->activityRepository->method('findAllByItinerary')->willReturn([$currentActivity, $nextActivity]);
        $this->entityManager->expects($this->once())->method('flush');
        $this->reportingClient->expects($this->once())->method('registerAttempt');

        $result = $this->service->completeActivity(1, 'A1', '1_0_2', 90);

        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('passed', $result);
        $this->assertEquals(1.0, $result['score']);
        $this->assertTrue($result['passed']);
    }

}
