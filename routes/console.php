<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('cleanup:old-jobs', function () {
    $thirtyDaysAgo = Carbon::now()->subDays(30);
    
    $deletedMarriage = DB::table('marriage_jobs')
        ->where('created_at', '<', $thirtyDaysAgo)
        ->delete();
        
    $deletedPlanets = DB::table('planets_jobs')
        ->where('created_at', '<', $thirtyDaysAgo)
        ->delete();
    
    $this->info("Deleted {$deletedMarriage} marriage jobs and {$deletedPlanets} planets jobs older than 30 days.");
})->purpose('Delete marriage and planets jobs older than 30 days');
