<?php

namespace App\Tests\Integration\Controller;

use App\Domain\Model\Activity;
use App\Domain\Model\Itinerary;
use App\Domain\Model\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActivityControllerTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->resetDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }

    public function test_get_next_activity_requires_authentication(): void
    {
        $url = '/api/getNextActivity?itinerary=additions&student_id=1';
        $this->client->request('GET', $url);
        $this->assertSame(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_complete_activity_requires_authentication(): void
    {
        $payload = ['activity_id' => 'A1', 'answers' => '1_0_2', 'time_spent' => 2];
        $this->client->request('POST', '/api/completeActivity', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload, JSON_THROW_ON_ERROR));
        $this->assertSame(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_get_next_activity_returns_first_activity_for_new_student(): void
    {
        $student = new Student();
        $student->setEmail('integration.alice@example.com');
        $student->setName('Integration Alice');
        $student->setRoles(['ROLE_USER']);
        $student->setPassword(password_hash('password123', PASSWORD_BCRYPT));
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        $this->client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'integration.alice@example.com',
            'password' => 'password123',
        ], JSON_THROW_ON_ERROR));

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $itinerary = new Itinerary();
        $itinerary->setName('Additions');
        $itinerary->setSlug('additions');

        $activity = new Activity();
        $activity->setIdentifier('A1');
        $activity->setName('Activity 1');
        $activity->setDifficulty(1);
        $activity->setPosition(1);
        $activity->setEstimatedTime(120);
        $activity->setSolution('1_0_2');
        $activity->setItinerary($itinerary);

        $this->entityManager->persist($itinerary);
        $this->entityManager->persist($activity);
        $this->entityManager->flush();

        $token = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['token'];
        $this->client->request('GET', '/api/getNextActivity?itinerary=additions&student_id=' . $student->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $data = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('A1', $data['identifier']);
    }

    private function resetDatabase(): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeStatement('TRUNCATE TABLE student_progress');
        $connection->executeStatement('TRUNCATE TABLE activities');
        $connection->executeStatement('TRUNCATE TABLE itineraries');
        $connection->executeStatement('TRUNCATE TABLE students');
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
    }
}
