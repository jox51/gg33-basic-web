<?php

namespace App\Http\Controllers;

use App\Models\MarriageJob;
use App\Utils\PocketBase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MarriageJobController extends Controller
{
    private PocketBase $magiPb;

    public function __construct()
    {
        $this->magiPb = new PocketBase(config('services.pocketbase.magi_url'));
    }

    public function getUserJobs(string $user_id): JsonResponse
    {
        try {
            // Get marriage jobs for the user, ordered by creation date desc
            $jobs = MarriageJob::where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get completed synastry charts from PocketBase
            $synastryCharts = $this->magiPb->getCollection('synastry_charts', [
                'filter' => "user_id = \"$user_id\"",
                'sort' => '-created'
            ]);

            // Create array of completed job IDs
            $completedCharts = collect($synastryCharts['items'])
                ->pluck('job_id')
                ->toArray();

            // Update status for completed jobs
            foreach ($jobs as $job) {
                if (in_array($job->job_id, $completedCharts) && $job->status !== 'completed') {
                    $job->status = 'completed';
                    $job->save();
                }
            }

            return response()->json([
                'message' => 'Couples compatibility jobs retrieved successfully',
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
            // Get synastry data from PocketBase
            $synastryData = $this->magiPb->getCollection('synastry_charts', [
                'filter' => "job_id = \"$job_id\"",
                'limit' => 1
            ]);

            if (empty($synastryData['items'])) {
                return response()->json([
                    'error' => 'Not found',
                    'message' => 'Marriage chart not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Marriage chart retrieved successfully',
                'data' => $synastryData['items'][0]['synastry_data']
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
            // Find the marriage job in our database
            $job = MarriageJob::where('job_id', $job_id)->firstOrFail();

            // Find and delete the record from PocketBase
            $synastryCharts = $this->magiPb->getCollection('synastry_charts', [
                'filter' => "job_id = \"$job_id\"",
                'limit' => 1
            ]);

            if (!empty($synastryCharts['items'])) {
                $recordId = $synastryCharts['items'][0]['id'];
                $this->magiPb->deleteRecord('synastry_charts', $recordId);
            }

            // Delete the job from our database
            $job->delete();

            return response()->json([
                'message' => 'Marriage chart deleted successfully',
                'job_id' => $job_id
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'Marriage job not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
