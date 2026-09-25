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

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(sprintf('Reporting rejected the attempt (HTTP %d)', $response->getStatusCode()));
        }
    }
}
