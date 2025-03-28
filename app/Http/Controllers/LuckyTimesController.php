<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\LuckyTimesService;
use App\Jobs\ProcessLuckyTimesJob;
class LuckyTimesController extends Controller
{
    protected $luckyTimesService;

    public function __construct(LuckyTimesService $luckyTimesService)
    {
        $this->luckyTimesService = $luckyTimesService;
    }
    public function store(Request $request): JsonResponse
    {
        Log::info('Raw Lucky Times Request: ' . json_encode($request->all()));
        try {

            Log::info('Lucky Times Request: ' . json_encode($request->all()));

            $cityInput = $request->input('city');
        
            // Clean up the city format
            $cityInput = preg_replace('/\s*,\s*/', ',', $cityInput); // Remove spaces around comma
            if (!str_contains($cityInput, ',')) {
                // If no comma exists, find the last space and replace with comma
                $cityInput = preg_replace('/\s+(?=[^\s]*$)/', ',', $cityInput);
            }
            
            // Get the current city value and clean it up
            $currentCityInput = $request->input('current_city');
            
            // Clean up the current city format
            $currentCityInput = preg_replace('/\s*,\s*/', ',', $currentCityInput); // Remove spaces around comma
            if (!str_contains($currentCityInput, ',')) {
                // If no comma exists, find the last space and replace with comma
                $currentCityInput = preg_replace('/\s+(?=[^\s]*$)/', ',', $currentCityInput);
            }
            
            // Set hour and minute values, ensuring they're integers
            $hour = $request->input('hour') !== null ? (int)$request->input('hour') : 12;
            $minute = $request->input('minute') !== null ? (int)$request->input('minute') : 0;
            // Transit Hour and Minute
            $transitHour = $request->input('transit_hour') !== null ? (int)$request->input('transit_hour') : 12;
            $transitMinute = $request->input('transit_minute') !== null ? (int)$request->input('transit_minute') : 0;
    
            $month = (int)$request->input('month'); // Converting to int removes leading zeros
            $day = (int)$request->input('day');     // Converting to int removes leading zeros
            
            // Generate from_date as current day
            $fromDate = now()->format('Y-m-d');
            
            // Merge the cleaned values back into the request with default values
            $request->merge([
                'city' => $cityInput,
                'current_city' => $currentCityInput,
                'hour' => $hour,
                'minute' => $minute,
                'transit_hour' => $transitHour,
                'transit_minute' => $transitMinute,
                'month' => $month,
                'day' => $day,
                'from_date' => $fromDate,  // Set the from_date as the current day
            ]);
            Log::info('Merged Request: ' . json_encode($request->all()));
        
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'year' => 'required|integer|between:1000,3000',
                'month' => 'required|integer|between:1,12',
                'day' => 'required|integer|between:1,31',
                'hour' => 'integer|between:0,23',
                'minute' => 'integer|between:0,59',
                'city' => 'required|string|max:255',
                'nation' => 'required|string|max:255',
                'current_city' => 'required|string|max:255',
                'current_nation' => 'required|string|max:255',
            ]);
            Log::info('Validated Request: ' . json_encode($validated));

            // Generate job_id
            $jobId = 'job_' . Str::random(8);

            // Generate from_date as current day
        $fromDate = now()->format('Y-m-d');
        Log::info('From Date: ' . $fromDate);

             // Structure the data for the chart request
        $chartData = [
            'name' => $validated['name'],
            'year' => $validated['year'],
            'month' => $validated['month'],
            'day' => $validated['day'],
            'hour' => $validated['hour'],
            'minute' => $validated['minute'],
            'city' => $validated['city'],
            'nation' => $validated['nation'],
            'current_city' => $validated['current_city'],
            'current_nation' => $validated['current_nation'],
            'user_id' => $request->input('user_id'),
            'job_id' => $jobId,
            'from_date' => $fromDate,
            'transit_hour' => "12",
            'transit_minute' => "00",
            'zodiac_type' => 'Sidereal',
            'sidereal_mode' => 'LAHIRI',
            'include_nakshatras' => false,
            'orb' =>  "1.0",
        ];
        Log::info('Lucky Times Chart Data: ' . json_encode($chartData));

            // Create planet job record
            $this->luckyTimesService->createLuckyTimesJob([
                'job_id' => $jobId,
                'user_id' =>  $request->input('user_id'),
                'status' => 'started',
                'name' => $this->luckyTimesService->extractFirstName($validated['name'])
            ]);

            // Dispatch job to queue
            ProcessLuckyTimesJob::dispatch([
                ...$validated,
                'job_id' => $jobId,
                'user_id' =>  $request->input('user_id'),
                'from_date' => $fromDate,
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
