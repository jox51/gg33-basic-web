<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSynastryJob;
use App\Services\SynastryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SynastryController extends Controller
{
    protected $synastryService;

    public function __construct(SynastryService $synastryService)
    {
        $this->synastryService = $synastryService;
    }

    public function process(Request $request)
    {
        try {

            // Generate job_id
            $jobId = 'job_' . Str::random(8);

            // Create marriage job record
            $this->synastryService->createMarriageJob([
                'job_id' => $jobId,
                'user_id' => $request->user_id,
                'status' => 'started',
                'names' => $this->synastryService->formatNames($request->name, $request->name2)
            ]);

            // Dispatch job to queue
            ProcessSynastryJob::dispatch([
                ...$request->all(),
                'job_id' => $jobId,
                'find_marriage_date' => false,
                'from_date' => null,
                'to_date' => null,
                'transit_hour' => null,
                'transit_minute' => null
            ]);

            return response()->json([
                'message' => 'Synastry data processed successfully',
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
