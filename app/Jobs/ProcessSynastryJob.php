<?php

namespace App\Jobs;

use App\Services\SynastryService;
use App\Models\MarriageJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSynastryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    public $timeout = 3600; // 1 hour timeout
    public $tries = 1; // Only try once since it's a long process

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(SynastryService $synastryService)
    {
        try {
            // Update status to processing
            $synastryService->updateJobStatus($this->data['job_id'], 'processing');

            // Process the request
            $result = $synastryService->processSynastryRequest($this->data);
            
            // Update job with success status and result
            $synastryService->updateJobStatus(
                $this->data['job_id'], 
                'completed', 
                result: $result
            );

            return $result;
        } catch (\Exception $e) {
            // Update job with error status
            $synastryService->updateJobStatus(
                $this->data['job_id'], 
                'failed', 
                error: $e->getMessage()
            );
            
            throw $e;
        }
    }
}