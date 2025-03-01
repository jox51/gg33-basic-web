<?php

use App\Http\Controllers\MarriageJobController;
use App\Http\Controllers\PlanetsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SynastryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    

});

    // Add new synastry route
Route::post('/charts/synastry-mobile', [SynastryController::class, 'process'])->name('charts.synastry-mobile');

Route::get('/jobs/user/{user_id}', [MarriageJobController::class, 'getUserJobs'])->name('jobs.user');

Route::post('/planets', [PlanetsController::class, 'store'])
    ->name('planets.store');

require __DIR__.'/auth.php';
