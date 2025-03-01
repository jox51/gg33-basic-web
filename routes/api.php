<?php

use App\Http\Controllers\MarriageJobController;
use App\Http\Controllers\PlanetJobController;
use App\Http\Controllers\PlanetsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SynastryController;

// These routes prefaced by api to be used by the mobile app
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/charts/synastry-mobile', [SynastryController::class, 'process'])
    ->name('charts.synastry-mobile');

Route::get('/jobs/user/{user_id}', [MarriageJobController::class, 'getUserJobs'])->name('jobs.user');

Route::get('/marriage-charts/{job_id}', [MarriageJobController::class, 'getJob'])
    ->name('marriage.charts.get');

Route::delete('/marriage-charts/{job_id}', [MarriageJobController::class, 'deleteJob'])
    ->name('marriage.charts.delete');

Route::post('/planets', [PlanetsController::class, 'store'])
    ->name('planets.store');

Route::get('/planets-jobs/{user_id}', [PlanetJobController::class, 'getUserJobs'])
        ->name('planets.jobs.user');

Route::get('/planets-jobs/job/{job_id}', [PlanetJobController::class, 'getJob'])
        ->name('planets.jobs.get');

        
Route::delete('/planets-jobs/{job_id}', [PlanetJobController::class, 'deleteJob'])
        ->name('planets.jobs.delete');

// Route::get('/charts/synastry-mobile/{jobId}', [SynastryController::class, 'checkStatus'])
//     ->name('charts.synastry-mobile.status');
