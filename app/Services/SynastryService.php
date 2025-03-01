<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\MarriageJob;

class SynastryService
{
    public function createMarriageJob(array $data)
    {
        // Implementation for creating marriage job record in your database
        // You'll need to create a model and migration for this
        return MarriageJob::create([
          'job_id' => $data['job_id'],
          'user_id' => $data['user_id'],
          'status' => $data['status'],
          'names' => $data['names']
      ]);
    }

    public function formatNames(string $name1, string $name2): string
    {
        return $this->extractFirstName($name1) . ' & ' . $this->extractFirstName($name2);
    }

    public function extractFirstName(string $fullName): string
    {
        return explode(' ', $fullName)[0];
    }

    public function processSynastryRequest(array $data)
    {
        $response = Http::post(
            config('services.mobile_api.base_url') . config('services.mobile_api.synastry_endpoint'),
            $data
        );

        return $response->json();
    }

    public function updateJobStatus(
        string $jobId, 
        string $status, 
        array $result = null, 
        string $error = null
    ): void {
        MarriageJob::where('job_id', $jobId)->update([
            'status' => $status,
            'result' => $result,
            'error' => $error,
        ]);
    }
}