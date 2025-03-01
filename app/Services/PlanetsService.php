<?php

namespace App\Services;

use App\Models\PlanetJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class PlanetsService
{
    public function createPlanetJob(array $data)
    {
        Log::info('Creating planet job: ' . json_encode($data));
        return PlanetJob::create([
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

    public function processPlanetsRequest(array $data)
    {
        Log::info('Processing planets request: ' . json_encode($data));
        $response = Http::post(
            config('services.mobile_api.base_url') . config('services.mobile_api.planets_endpoint'),
            $data
        );

        Log::info('Planets request response: ' . $response->body());

        return $response->json();
    }

    public function updateJobStatus(
        string $jobId, 
        string $status, 
        array $result = null, 
        string $error = null
    ): void {

        Log::info('Updating job status: ' . $status);
        PlanetJob::where('job_id', $jobId)->update([
            'status' => $status,
            'result' => $result,
            'error' => $error,
        ]);
    }
}