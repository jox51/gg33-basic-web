<?php

namespace App\Http\Controllers;

use App\Models\PlanetJob;
use App\Utils\PocketBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
class PlanetJobController extends Controller
{
    private PocketBase $magiPb;

    public function __construct()
    {
        $this->magiPb = new PocketBase(config('services.pocketbase.magi_url'));
    }

    public function getUserJobs(string $user_id): JsonResponse
    {
        try {
            // Get planet jobs for the user, ordered by creation date desc
            $jobs = PlanetJob::where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get completed planets from PocketBase
            $planetRecords = $this->magiPb->getCollection('planets', [
                'filter' => "user_id = \"$user_id\"",
                'sort' => '-created'
            ]);

            // Create array of completed job IDs
            $completedJobs = collect($planetRecords['items'])
                ->pluck('job_id')
                ->toArray();

            // Update status for completed jobs
            foreach ($jobs as $job) {
                if (in_array($job->job_id, $completedJobs) && $job->status !== 'completed') {
                    $job->status = 'completed';
                    $job->save();
                }
            }

            return response()->json([
                'message' => 'Planets jobs retrieved successfully',
                'data' => $jobs
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getJob(string $job_id): JsonResponse
    {
        try {
            // Get planet data from PocketBase
            $planetData = $this->magiPb->getCollection('planets', [
                'filter' => "job_id = \"$job_id\"",
                'limit' => 1
            ]);

            Log::info('Planet data: ' . json_encode($planetData));

            if (empty($planetData['items'])) {
                return response()->json([
                    'error' => 'Not found',
                    'message' => 'Planet chart not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Planet chart retrieved successfully',
                'data' => $planetData['items'][0]['planets_data']
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteJob(string $job_id): JsonResponse
    {
        try {
            // Find the planet job in our database
            $job = PlanetJob::where('job_id', $job_id)->firstOrFail();

            // Find and delete the record from PocketBase
            $planetRecords = $this->magiPb->getCollection('planets', [
                'filter' => "job_id = \"$job_id\"",
                'limit' => 1
            ]);

            if (!empty($planetRecords['items'])) {
                $recordId = $planetRecords['items'][0]['id'];
                $this->magiPb->deleteRecord('planets', $recordId);
            }

            // Delete the job from our database
            $job->delete();

            return response()->json([
                'message' => 'Planet chart deleted successfully',
                'job_id' => $job_id
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'Planet job not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
