<?php

namespace App\Exceptions;

use App\Services\ErrorMonitoringService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (config('audit.monitoring.enabled') && app()->environment() !== 'testing') {
                app(ErrorMonitoringService::class)->capture($e);
            }
        });
    }
}
