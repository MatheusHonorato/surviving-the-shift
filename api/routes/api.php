<?php

declare(strict_types=1);

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AnswerKeyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PersonalReportController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::post('answers/start', [AnswerController::class, 'start']);
    Route::post('answers/finish', [AnswerController::class, 'finish']);
    Route::post('answers', [AnswerController::class, 'store']);

    Route::get('patients/{id}', [PatientController::class, 'show']);
    Route::get('patients', [PatientController::class, 'index']);

    Route::get('steps/{id}', [StepController::class, 'show']);
    Route::get('steps', [StepController::class, 'index']);

    Route::get('answer-keys/{id}', [AnswerKeyController::class, 'show']);
    Route::get('answer-keys', [AnswerKeyController::class, 'index']);

    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('personal-report', [PersonalReportController::class, 'index']);
});
