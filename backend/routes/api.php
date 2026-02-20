<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\MarketingController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\FeeP3smController;
use App\Http\Controllers\Api\DashboardController;

// ========== Public Routes ==========
Route::post('/login', [AuthController::class, 'login']);

// ========== Protected Routes (Sanctum) ==========
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Users CRUD
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Certificates CRUD + Reference Data
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::post('/certificates', [CertificateController::class, 'store']);
    Route::put('/certificates/reference-data', [CertificateController::class, 'updateReferenceData']);
    Route::put('/certificates/{id}', [CertificateController::class, 'update']);
    Route::delete('/certificates/{id}', [CertificateController::class, 'destroy']);

    // Marketing CRUD
    Route::get('/marketing', [MarketingController::class, 'index']);
    Route::post('/marketing', [MarketingController::class, 'store']);
    Route::put('/marketing/{id}', [MarketingController::class, 'update']);
    Route::delete('/marketing/{id}', [MarketingController::class, 'destroy']);

    // Submissions CRUD
    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::put('/submissions/{id}', [SubmissionController::class, 'update']);
    Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy']);

    // Transactions CRUD
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

    // Fee P3SM CRUD
    Route::get('/fee-p3sm', [FeeP3smController::class, 'index']);
    Route::post('/fee-p3sm', [FeeP3smController::class, 'store']);
    Route::put('/fee-p3sm/{id}', [FeeP3smController::class, 'update']);
    Route::delete('/fee-p3sm/{id}', [FeeP3smController::class, 'destroy']);

    // Dashboard
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/ranking', [DashboardController::class, 'ranking']);
});
