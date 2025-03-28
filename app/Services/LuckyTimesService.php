<?php

namespace App\Services;

use App\Models\LuckyTimesJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class LuckyTimesService
{
    public function createLuckyTimesJob(array $data)
    {
        Log::info('Creating lucky times job: ' . json_encode($data));
        return LuckyTimesJob::create([
            'job_id' => $data['job_id'],
            'user_id' => $data['user_id'],
            'status' => $data['status'],
            'name' => $data['name']
        ]);
    }

    public function extractFirstName(string $fullName): string
    {
        return explode(' ', $fullName)[0];
    }

    public function processLuckyTimesRequest(array $data)
    {
        Log::info('Processing lucky times request: ' . json_encode($data));
        Log::info('Base URL: ' . config('services.mobile_api.base_url'));
        Log::info('Endpoint: ' . config('services.mobile_api.lucky_times_endpoint'));
        $baseUrl = config('services.mobile_api.base_url');
        $endpoint = config('services.mobile_api.lucky_times_endpoint') ?: '/charts/vedic-lucky-times';
        $fullUrl = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        Log::info('Full Request URL: ' . $fullUrl);
        $response = Http::post($fullUrl, $data);
        Log::info('Post Request URL: ' . $fullUrl);

        Log::info('Lucky times request response: ' . $response->body());

        return $response->json();
    }

    public function updateJobStatus(
        string $jobId, 
        string $status, 
        array $result = null, 
        string $error = null
    ): void {

        Log::info('Updating job status: ' . $status);
        LuckyTimesJob::where('job_id', $jobId)->update([
            'status' => $status,
            'result' => $result,
            'error' => $error,
        ]);
    }
}