<?php

namespace App\Jobs;

use App\Services\LuckyTimesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLuckyTimesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    public $timeout = 3600; // 1 hour timeout
    public $tries = 1; // Only try once since it's a long process

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(LuckyTimesService $luckyTimesService)
    {
        try {
            Log::info('Processing lucky times job: ' . json_encode($this->data));
            // Update status to processing
            $luckyTimesService->updateJobStatus($this->data['job_id'], 'processing');

           

            // Process the request
            $result = $luckyTimesService->processLuckyTimesRequest($this->data);
            
            // Update job with success status and result
            $luckyTimesService->updateJobStatus(
                $this->data['job_id'], 
                'completed', 
                result: $result
            );

            return $result;
        } catch (\Exception $e) {
            // Update job with error status
            $luckyTimesService->updateJobStatus(
                $this->data['job_id'], 
                'failed', 
                error: $e->getMessage()
            );
            
            throw $e;
        }
    }
}
