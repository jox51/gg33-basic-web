<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPlanetsJob;
use App\Services\PlanetsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PlanetsController extends Controller
{
    protected $planetsService;

    public function __construct(PlanetsService $planetsService)
    {
        $this->planetsService = $planetsService;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string',
                'year' => 'required',
                'month' => 'required',
                'day' => 'required',
                'hour' => 'required',
                'minute' => 'required',
                'city' => 'required|string',
                'nation' => 'required|string',
                'user_id' => 'required|string',
            ]);

            // Generate job_id
            $jobId = 'job_' . Str::random(8);

            // Create planet job record
            $this->planetsService->createPlanetJob([
                'job_id' => $jobId,
                'user_id' => $validated['user_id'],
                'status' => 'started',
                'name' => $this->planetsService->extractFirstName($validated['name'])
            ]);

            // Dispatch job to queue
            ProcessPlanetsJob::dispatch([
                ...$validated,
                'job_id' => $jobId
            ]);

            return response()->json([
                'message' => 'Planets data processing started',
                'job_id' => $jobId
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
