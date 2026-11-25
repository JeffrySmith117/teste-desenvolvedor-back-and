<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\VacancyController;
use Illuminate\Support\Facades\Route;

// Prefixo "api." nos nomes para não conflitar com as rotas web (vacancies.index etc.).
Route::name('api.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::delete('/vacancies/bulk', [VacancyController::class, 'bulkDestroy'])->name('vacancies.bulk-destroy');
        Route::patch('/vacancies/{vacancy}/toggle-pause', [VacancyController::class, 'togglePause'])->name('vacancies.toggle-pause');
        Route::apiResource('vacancies', VacancyController::class);

        Route::delete('/candidates/bulk', [CandidateController::class, 'bulkDestroy'])->name('candidates.bulk-destroy');
        Route::apiResource('candidates', CandidateController::class);

        Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
        Route::delete('/candidates/{candidate}/vacancies/{vacancy}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
    });
});
