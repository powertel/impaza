<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\FaultController;
use App\Http\Controllers\Mobile\StatsController;
use App\Http\Controllers\Mobile\ProfileController;
use App\Http\Controllers\Api\CustomerFaultsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Mobile API routes

Route::prefix('mobile')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        // Profile endpoints
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::post('profile/password', [ProfileController::class, 'changePassword']);

        Route::get('faults', [FaultController::class, 'index']);
        Route::get('faults/{fault}', [FaultController::class, 'show']);
        Route::post('faults/{fault}/rectify', [FaultController::class, 'rectify']);
        Route::post('faults/{fault}/escalate', [FaultController::class, 'escalate']);
        Route::post('faults/{fault}/remarks', [FaultController::class, 'addRemark']);
        Route::get('rfos', [FaultController::class, 'rfos']);
        Route::get('sections', [FaultController::class, 'sections']);
        Route::get('technician-stats', [StatsController::class, 'myStats']);
    });
});

// Customer faults lookup by account and contract
Route::middleware('api_key')->get('customer-faults', [CustomerFaultsController::class, 'index']);
