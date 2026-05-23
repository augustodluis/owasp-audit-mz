<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/audits', [AuditController::class, 'apiIndex']);
    Route::get('/audits/{audit}', [AuditController::class, 'apiShow']);

    Route::get('/notifications', [NotificationController::class, 'apiIndex']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'apiMarkRead']);
});
