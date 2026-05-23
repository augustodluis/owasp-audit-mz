<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VulnerabilityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [AuditController::class, 'index'])->name('dashboard');

    Route::get('/audits/create', [AuditController::class, 'create'])->name('audits.create');
    Route::post('/audits', [AuditController::class, 'store'])->name('audits.store');
    Route::get('/audits/{audit}', [AuditController::class, 'show'])->name('audits.show');
    Route::delete('/audits/{audit}', [AuditController::class, 'destroy'])->name('audits.destroy');

    Route::get('/audits/{audit}/report', [ReportController::class, 'show'])->name('audits.report');
    Route::get('/audits/{audit}/report.pdf', [ReportController::class, 'pdf'])->name('audits.report.pdf');
    Route::get('/audits/{audit}/report.csv', [AuditController::class, 'exportCsv'])->name('audits.report.csv');
    Route::post('/audits/{audit}/resend', [AuditController::class, 'resend'])->name('audits.resend');
    Route::get('/audits/{audit}/compare', [AuditController::class, 'compare'])->name('audits.compare');

    Route::patch('/vulnerabilities/{vulnerability}', [VulnerabilityController::class, 'update'])
        ->name('vulnerabilities.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/errors', [ErrorController::class, 'index'])->name('errors');
        Route::patch('/errors/{errorLog}/resolve', [ErrorController::class, 'resolve'])->name('errors.resolve');
    });
});
