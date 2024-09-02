<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

class DebrickedApiService
{
    private $client;
    private $jwtToken;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private function authenticate(): string
    {
        if (!$this->jwtToken) {
            $response = $this->client->request('POST', 'https://debricked.com/api/login_check', [
                'json' => [
                    '_username' => 'nupurbora@outlook.com',
                    '_password' => 'Gravewizard#10',
                ],
            ]);

            $this->jwtToken = $response->toArray()['token'];
        }

        return $this->jwtToken;
    }

    public function uploadAndScanFile(string $filePath): array
    {
        $jwtToken = $this->authenticate();

        try {
            $response = $this->client->request('POST', 'https://debricked.com/api/1.0/open/uploads/dependencies/files', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $jwtToken,
                    'Content-Type' => 'multipart/form-data',
                ],
                'body' => [
                    'fileData' => fopen($filePath, 'r'),
                    'repositoryName' => 'https://github.com/nupurbora10/debricked',
                    'commitName' => 'initial commit'
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                $result['message'] = 'File upload failed';
                return $result;
            }

            $fileId = $response->toArray()['id'];

            // for scan result
            $result = $this->client->request('GET', 'https://debricked.com/api/1.0/open/scan/scan-status', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $jwtToken,
                ],
            ]);

            return $result->toArray();
        } catch (ExceptionInterface $e) {
            // Log the error and rethrow it
            throw new \RuntimeException('Failed to scan file: ' . $e->getMessage());
        }
    }
}