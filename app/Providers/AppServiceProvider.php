<?php

namespace App\Providers;

use App\Services\ScannerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ScannerService::class, function () {
            $scanner = new ScannerService();
            foreach (config('audit.checks', []) as $checkClass) {
                $scanner->register(app($checkClass));
            }
            return $scanner;
        });
    }

    public function boot(): void
    {
    }
}
