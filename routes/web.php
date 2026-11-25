<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::redirect('/', '/vacancies');

    Route::delete('/vacancies/bulk', [VacancyController::class, 'bulkDestroy'])->name('vacancies.bulk-destroy');
    Route::patch('/vacancies/{vacancy}/toggle-pause', [VacancyController::class, 'togglePause'])->name('vacancies.toggle-pause');
    Route::resource('vacancies', VacancyController::class);

    Route::delete('/candidates/bulk', [CandidateController::class, 'bulkDestroy'])->name('candidates.bulk-destroy');
    Route::resource('candidates', CandidateController::class);

    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::delete('/candidates/{candidate}/vacancies/{vacancy}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
});
