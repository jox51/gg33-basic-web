<?php

namespace App\Http\Controllers;

use App\Models\LuckyTimesJob;
use App\Utils\PocketBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
class LuckyTimesJobController extends Controller
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
            $jobs = LuckyTimesJob::where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get completed planets from PocketBase
            $luckyTimesRecords = $this->magiPb->getCollection('lucky_times_vedic', [
                'filter' => "user_id = \"$user_id\"",
                'sort' => '-created'
            ]);

            // Create array of completed job IDs
            $completedJobs = collect($luckyTimesRecords['items'])
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
                'message' => 'Lucky times jobs retrieved successfully',
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
            $luckyTimesData = $this->magiPb->getCollection('lucky_times_vedic', [
                'filter' => "job_id = \"$job_id\"",
                'limit' => 1
            ]);

            Log::info('Lucky times data: ' . json_encode($luckyTimesData));

            if (empty($luckyTimesData['items'])) {
                return response()->json([
                    'error' => 'Not found',
                    'message' => 'Lucky times chart not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Lucky times chart retrieved successfully',
                'data' => $luckyTimesData['items'][0]['lucky_times_data']
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
            $job = LuckyTimesJob::where('job_id', $job_id)->firstOrFail();

            // Find and delete the record from PocketBase
            $luckyTimesRecords = $this->magiPb->getCollection('lucky_times_vedic', [
                'filter' => "job_id = \"$job_id\"",
                'limit' => 1
            ]);

            if (!empty($luckyTimesRecords['items'])) {
                $recordId = $luckyTimesRecords['items'][0]['id'];
                $this->magiPb->deleteRecord('lucky_times_vedic', $recordId);
            }

            // Delete the job from our database
            $job->delete();

            return response()->json([
                'message' => 'Lucky times chart deleted successfully',
                'job_id' => $job_id
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Not found',
                'message' => 'Lucky times job not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
