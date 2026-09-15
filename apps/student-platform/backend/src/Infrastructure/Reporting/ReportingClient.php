<?php

namespace App\Infrastructure\Reporting;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReportingClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $baseUrl,
    ) {
    }

    public function registerAttempt(array $attempt): void
    {
        $response = $this->httpClient->request('POST', $this->baseUrl . '/attempts', [
            'json' => $attempt,
        ]);

        // Make sure the reporting service has registered the attempt before continuing
        $response->getContent();
    }
}
