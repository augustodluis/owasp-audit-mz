<?php

namespace App\Providers;

use App\Models\Audit;
use App\Policies\AuditPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Audit::class => AuditPolicy::class,
    ];

    public function boot(): void
    {
    }
}
