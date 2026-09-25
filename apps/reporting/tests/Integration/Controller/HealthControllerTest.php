<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthControllerTest extends WebTestCase
{
    public function test_health_returns_ok(): void
    {
        $client = static::createClient();
        $client->request('GET', '/health');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(['status' => 'ok'], json_decode($client->getResponse()->getContent(), true));
    }
}
