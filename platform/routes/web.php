<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdminController::class, 'index']);

Route::prefix('admin')->name('admin.')->controller(AdminController::class)->group(function (): void {
    Route::get('/', 'index')->name('dashboard');
    Route::get('/applications', 'index')->defaults('section', 'applications')->name('applications.index');
    Route::get('/professionals', 'index')->defaults('section', 'professionals')->name('professionals.index');
    Route::get('/devices', 'index')->defaults('section', 'devices')->name('devices.index');
    Route::get('/versions', 'index')->defaults('section', 'versions')->name('versions.index');
    Route::get('/flags', 'index')->defaults('section', 'flags')->name('flags.index');
    Route::get('/notices', 'index')->defaults('section', 'notices')->name('notices.index');
    Route::get('/notifications', 'index')->defaults('section', 'notifications')->name('notifications.index');
    Route::get('/support', 'index')->defaults('section', 'support')->name('support.index');

    Route::post('/applications', 'storeApplication')->name('applications.store');
    Route::put('/applications/{application}', 'updateApplication')->name('applications.update');
    Route::delete('/applications/{application}', 'destroyApplication')->name('applications.destroy');

    Route::post('/versions', 'storeVersion')->name('versions.store');
    Route::put('/versions/{version}', 'updateVersion')->name('versions.update');
    Route::delete('/versions/{version}', 'destroyVersion')->name('versions.destroy');

    Route::post('/flags', 'storeFlag')->name('flags.store');
    Route::put('/flags/{flag}', 'updateFlag')->name('flags.update');
    Route::delete('/flags/{flag}', 'destroyFlag')->name('flags.destroy');

    Route::post('/notices', 'storeNotice')->name('notices.store');
    Route::put('/notices/{notice}', 'updateNotice')->name('notices.update');
    Route::delete('/notices/{notice}', 'destroyNotice')->name('notices.destroy');

    Route::post('/notifications', 'storeNotification')->name('notifications.store');
    Route::put('/notifications/{notification}', 'updateNotification')->name('notifications.update');
    Route::delete('/notifications/{notification}', 'destroyNotification')->name('notifications.destroy');

    Route::patch('/professionals/{professional}/toggle', 'toggleProfessional')->name('professionals.toggle');
    Route::patch('/devices/{device}/toggle', 'toggleDevice')->name('devices.toggle');
});
