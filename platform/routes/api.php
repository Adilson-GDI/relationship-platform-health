<?php

use App\Http\Controllers\Api\ProfessionalRegistrationController;
use App\Http\Controllers\Api\AppClientController;
use App\Http\Controllers\Api\ServiceLocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/professionals/register', [ProfessionalRegistrationController::class, 'store']);
    Route::get('/apps/{app_code}/bootstrap', [ProfessionalRegistrationController::class, 'bootstrap']);

    Route::post('/app_users/register', [AppClientController::class, 'registerUser']);
    Route::post('/fcm_tokens/register', [AppClientController::class, 'registerFcmToken']);
    Route::post('/app/status', [AppClientController::class, 'status']);
    Route::post('/support/messages', [AppClientController::class, 'supportMessage']);
    Route::post('/service-locations/sync', [ServiceLocationController::class, 'sync']);
});
