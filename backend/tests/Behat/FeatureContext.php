<?php

namespace App\Tests\Behat;

use App\Domain\Model\Activity;
use App\Domain\Model\Itinerary;
use App\Domain\Model\StudentProgress;
use App\Domain\Service\ScoreCalculator;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\KernelInterface;
class FeatureContext implements Context
{
    private KernelInterface $kernel;
    private EntityManagerInterface $em;
    private KernelBrowser $client;
    private ?array $lastResponse = null;
    private int $lastStatusCode = 0;
    private float $calculatedScore = 0.0;
    private ?string $currentSolution = null;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->client = new KernelBrowser($kernel);
    }

    /**
     * @Given the database is clean
     */
    public function theDatabaseIsClean(): void
    {
        $conn = $this->em->getConnection();
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS=0');
        $conn->executeStatement('TRUNCATE TABLE student_progress');
        $conn->executeStatement('TRUNCATE TABLE activities');
        $conn->executeStatement('TRUNCATE TABLE itineraries');
        $conn->executeStatement('TRUNCATE TABLE students');
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @Given the fixtures are loaded
     */
    public function theFixturesAreLoaded(): void
    {
        $conn = $this->em->getConnection();

        $conn->executeStatement("INSERT INTO itineraries (id, name, slug) VALUES (1, 'Additions', 'additions')");

        $activities = [
            [1, 'A1', 'Activity 1', 1, 1, 120, '1_0_2'],
            [2, 'A2', 'Activity 2', 2, 1, 60, '-2_40_56'],
            [3, 'A3', 'Activity 3', 3, 1, 120, '1_0'],
            [15, 'A15', 'Activity 15', 15, 10, 120, '1_0_2'],
        ];

        foreach ($activities as [$id, $identifier, $name, $position, $difficulty, $estimatedTime, $solution]) {
            $conn->executeStatement(
                "INSERT INTO activities (id, identifier, name, position, difficulty, estimated_time, solution, itinerary_id) VALUES ($id, '$identifier', '$name', $position, $difficulty, $estimatedTime, '$solution', 1)"
            );
        }

        $hashedPassword = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 13]);
        $conn->executeStatement(
            "INSERT INTO students (id, email, name, roles, password) VALUES (1, 'alice@innovamat.com', 'Alice Smith', '[\"ROLE_USER\"]', '$hashedPassword')"
        );
        $conn->executeStatement(
            "INSERT INTO students (id, email, name, roles, password) VALUES (2, 'bob@innovamat.com', 'Bob Jones', '[\"ROLE_USER\"]', '$hashedPassword')"
        );
        $conn->executeStatement(
            "INSERT INTO student_progress (student_id, itinerary_id, current_activity_id, completed, last_score, started_at) VALUES (2, 1, 2, 0, 1.0, NOW())"
        );
    }

    /**
     * @Given I am authenticated as :email with password :password
     */
    public function iAmAuthenticatedAs(string $email, string $password): void
    {
        $this->client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['email' => $email, 'password' => $password]));

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . ($data['token'] ?? ''));
    }

    /**
     * @Given student :studentId is on activity :activityIdentifier in itinerary :itinerarySlug
     */
    public function studentIsOnActivity(int $studentId, string $activityIdentifier, string $itinerarySlug): void
    {
        $conn = $this->em->getConnection();
        $activityId = $conn->fetchOne("SELECT id FROM activities WHERE identifier = '$activityIdentifier'");
        $itineraryId = $conn->fetchOne("SELECT id FROM itineraries WHERE slug = '$itinerarySlug'");

        $existing = $conn->fetchOne(
            "SELECT id FROM student_progress WHERE student_id = $studentId AND itinerary_id = $itineraryId"
        );

        if ($existing) {
            $conn->executeStatement(
                "UPDATE student_progress SET current_activity_id = $activityId WHERE student_id = $studentId AND itinerary_id = $itineraryId"
            );
        } else {
            $conn->executeStatement(
                "INSERT INTO student_progress (student_id, itinerary_id, current_activity_id, completed, started_at) VALUES ($studentId, $itineraryId, $activityId, 0, NOW())"
            );
        }
    }

    /**
     * @When I request the next activity for itinerary :slug and student :studentId
     */
    public function iRequestNextActivity(string $slug, int $studentId): void
    {
        $this->client->request('GET', "/api/getNextActivity?itinerary=$slug&student_id=$studentId");
        $this->lastStatusCode = $this->client->getResponse()->getStatusCode();
        $this->lastResponse = json_decode($this->client->getResponse()->getContent(), true);
    }

    /**
     * @When I complete activity :activityId for student :studentId with answers :answers in :minutes minutes
     */
    public function iCompleteActivity(string $activityId, int $studentId, string $answers, int $minutes): void
    {
        $this->client->request('POST', '/api/completeActivity', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'activity_id' => $activityId,
            'student_id' => $studentId,
            'answers' => $answers,
            'time_spent' => $minutes,
        ]));
        $this->lastStatusCode = $this->client->getResponse()->getStatusCode();
        $this->lastResponse = json_decode($this->client->getResponse()->getContent(), true);
    }

    /**
     * @Given activity :id has solution :solution
     */
    public function activityHasSolution(string $id, string $solution): void
    {
        $this->currentSolution = $solution;
    }

    /**
     * @When I calculate the score for answers :answers against solution :solution
     */
    public function iCalculateScoreForAnswers(string $answers, string $solution): void
    {
        $this->calculatedScore = ScoreCalculator::calculate($answers, $solution);
    }

    /**
     * @Then the calculated score should be :expectedScore
     */
    public function theCalculatedScoreShouldBe(float $expectedScore): void
    {
        if ($this->calculatedScore !== $expectedScore) {
            throw new \RuntimeException(
                sprintf('Expected score %s but got %s', $expectedScore, $this->calculatedScore)
            );
        }
    }

    /**
     * @Then the response status code should be :code
     */
    public function theResponseStatusCodeShouldBe(int $code): void
    {
        if ($this->lastStatusCode !== $code) {
            throw new \RuntimeException(
                sprintf('Expected status %d but got %d. Response: %s', $code, $this->lastStatusCode, json_encode($this->lastResponse))
            );
        }
    }

    /**
     * @Then the response should contain activity with identifier :identifier
     */
    public function theResponseShouldContainActivityWithIdentifier(string $identifier): void
    {
        if (!isset($this->lastResponse['identifier']) || $this->lastResponse['identifier'] !== $identifier) {
            throw new \RuntimeException(
                sprintf('Expected activity "%s" but got: %s', $identifier, json_encode($this->lastResponse))
            );
        }
    }

    /**
     * @Then the response should contain :key
     */
    public function theResponseShouldContain(string $key): void
    {
        if (!array_key_exists($key, $this->lastResponse ?? [])) {
            throw new \RuntimeException(sprintf('Response does not contain key "%s"', $key));
        }
    }

    /**
     * @Then the field :field should be true
     */
    public function theFieldShouldBeTrue(string $field): void
    {
        if (($this->lastResponse[$field] ?? null) !== true) {
            throw new \RuntimeException(
                sprintf('Expected field "%s" to be true, got: %s', $field, json_encode($this->lastResponse[$field] ?? null))
            );
        }
    }

    /**
     * @Then the field :field should be false
     */
    public function theFieldShouldBeFalse(string $field): void
    {
        if (($this->lastResponse[$field] ?? null) !== false) {
            throw new \RuntimeException(
                sprintf('Expected field "%s" to be false, got: %s', $field, json_encode($this->lastResponse[$field] ?? null))
            );
        }
    }

    /**
     * @Then the field :field should equal :value
     */
    public function theFieldShouldEqual(string $field, float $value): void
    {
        if (($this->lastResponse[$field] ?? null) != $value) {
            throw new \RuntimeException(
                sprintf('Expected field "%s" to equal %s, got: %s', $field, $value, json_encode($this->lastResponse[$field] ?? null))
            );
        }
    }
}
