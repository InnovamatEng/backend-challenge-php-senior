<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AttemptsControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        static::getContainer()->get('doctrine.dbal.default_connection')->executeStatement('TRUNCATE TABLE activity_stats');
    }

    public function test_first_attempt_creates_the_activity_stats(): void
    {
        $this->postAttempt(['score' => 1.0, 'passed' => true, 'time_spent' => 90]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $stats = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('A1', $stats['activity_id']);
        $this->assertSame(1, $stats['attempts_count']);
        $this->assertSame(1, $stats['passed_count']);
        $this->assertEquals(1.0, $stats['average_score']);
        $this->assertSame(90, $stats['average_time_spent']);
    }

    public function test_following_attempts_are_folded_into_the_stats(): void
    {
        $this->postAttempt(['score' => 1.0, 'passed' => true, 'time_spent' => 90]);
        $this->postAttempt(['score' => 0.5, 'passed' => false, 'time_spent' => 150]);

        $stats = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame(2, $stats['attempts_count']);
        $this->assertSame(1, $stats['passed_count']);
        $this->assertEquals(0.75, $stats['average_score']);
        $this->assertSame(120, $stats['average_time_spent']);
    }

    public function test_missing_field_is_rejected(): void
    {
        $this->client->request('POST', '/attempts', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'student_id' => 1,
            'activity_id' => 'A1',
        ]));

        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    public function test_invalid_body_is_rejected(): void
    {
        $this->client->request('POST', '/attempts', [], [], ['CONTENT_TYPE' => 'application/json'], 'not json');

        $this->assertSame(400, $this->client->getResponse()->getStatusCode());
    }

    private function postAttempt(array $overrides): void
    {
        $this->client->request('POST', '/attempts', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($overrides + [
            'student_id' => 1,
            'activity_id' => 'A1',
            'itinerary' => 'additions',
            'score' => 1.0,
            'passed' => true,
            'time_spent' => 90,
            'completed_at' => '2026-09-17T10:00:00+00:00',
        ]));
    }
}
