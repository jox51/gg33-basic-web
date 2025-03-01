<?php

namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PocketBase
{
    private Client $client;
    private string $baseUrl;
    private string $username;
    private string $password;

    public function __construct(string $baseUrl)
    {
        $this->client = new Client();
        $this->baseUrl = $baseUrl;
        $this->username = config('services.pocketbase.username');
        $this->password = config('services.pocketbase.password');
    }

    public function authenticate(): string
    {
        // Check if we have a valid token in cache
        $cacheKey = 'pocketbase_token_' . md5($this->baseUrl);
        $token = Cache::get($cacheKey);

        if ($token) {
            return $token;
        }

        try {
            $response = $this->client->post($this->baseUrl . '/api/collections/_superusers/auth-with-password', [
                'json' => [
                    'identity' => $this->username,
                    'password' => $this->password
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $token = $data['token'];

            // Cache the token for 6 hours (adjust as needed)
            Cache::put($cacheKey, $token, now()->addHours(6));

            return $token;
        } catch (GuzzleException $e) {
            Log::error('PocketBase authentication failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCollection(string $collection, array $params = []): array
    {
        try {
            $token = $this->authenticate();

            $response = $this->client->get($this->baseUrl . '/api/collections/' . $collection . '/records', [
                'headers' => [
                    'Authorization' => $token
                ],
                'query' => $params
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('PocketBase collection fetch failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteRecord(string $collection, string $recordId): void
    {
        try {
            $token = $this->authenticate();

            $this->client->delete($this->baseUrl . '/api/collections/' . $collection . '/records/' . $recordId, [
                'headers' => [
                    'Authorization' => $token
                ]
            ]);
        } catch (GuzzleException $e) {
            Log::error('PocketBase record deletion failed: ' . $e->getMessage());
            throw $e;
        }
    }
}