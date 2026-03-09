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
        Route::get('faults/unassigned', [FaultController::class, 'unassigned']);
        Route::get('faults/section', [FaultController::class, 'sectionFaults']);
        Route::get('faults/assessments', [FaultController::class, 'assessments']);
        Route::get('faults/rectified', [FaultController::class, 'rectified']);
        Route::get('faults/escalations', [FaultController::class, 'escalations']);
        Route::get('faults/resolved', [FaultController::class, 'resolved']);
        Route::get('faults/referred', [FaultController::class, 'referred']);

        Route::get('faults/{fault}', [FaultController::class, 'show']);
        Route::post('faults/assign', [FaultController::class, 'assign']);
        Route::post('faults/{id}/reassign', [FaultController::class, 'reassign']);
        Route::post('faults/{id}/reassign-referral', [FaultController::class, 'reassignReferral']);
        Route::post('faults/{id}/complete-referral', [FaultController::class, 'completeReferral']);
        Route::post('faults/{id}/assess', [FaultController::class, 'assess']);
        Route::post('faults/{id}/clear', [FaultController::class, 'clear']);
        Route::post('faults/{id}/revoke', [FaultController::class, 'revoke']);
        Route::post('faults/{id}/refer', [FaultController::class, 'refer']);
        
        Route::get('technicians/assignable', [FaultController::class, 'assignableTechnicians']);
        
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
