<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReportsControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        static::getContainer()->get('doctrine.dbal.default_connection')->executeStatement('TRUNCATE TABLE activity_stats');
    }

    public function test_lists_the_stats_of_every_activity(): void
    {
        foreach (['A2', 'A1'] as $activityId) {
            $this->client->request('POST', '/attempts', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'student_id' => 1,
                'activity_id' => $activityId,
                'itinerary' => 'additions',
                'score' => 1.0,
                'passed' => true,
                'time_spent' => 90,
                'completed_at' => '2026-09-17T10:00:00+00:00',
            ]));
        }

        $this->client->request('GET', '/reports/activities');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $report = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame(['A1', 'A2'], array_column($report, 'activity_id'));
    }
}
