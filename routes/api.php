<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\StudentController;
use App\Http\Controllers\v1\AuthController;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\v1\ScheduleController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\v1\StripeCheckoutController;
use App\Http\Controllers\v1\StudentProgressController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::apiResource('/schedule', ScheduleController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/stripe/checkout-session', [StripeCheckoutController::class, 'create']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/students', StudentController::class);

    Route::get('/students/{id}/progress', [StudentProgressController::class, 'myProgress']);

    Route::get('/user/{id}', [ScheduleController::class, 'user']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/schedule', [ScheduleController::class, 'currentSchedule']);
    Route::patch('/schedule/reschedule', [ScheduleController::class, 'switchClassSessions']);


    Route::get('/schedule/class/{id}', [ClassSessionController::class, 'show']);
});