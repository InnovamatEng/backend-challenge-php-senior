<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AttemptsControllerTest extends WebTestCase
{
    public function test_attempt_is_accepted(): void
    {
        $client = static::createClient();
        $client->request('POST', '/attempts', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'student_id' => 1,
            'activity_id' => 'A1',
            'score' => 1.0,
            'time_spent' => 90,
        ]));

        $this->assertSame(202, $client->getResponse()->getStatusCode());
        $this->assertSame(['status' => 'accepted'], json_decode($client->getResponse()->getContent(), true));
    }

    public function test_invalid_body_is_rejected(): void
    {
        $client = static::createClient();
        $client->request('POST', '/attempts', [], [], ['CONTENT_TYPE' => 'application/json'], 'not json');

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }
}
