<?php

namespace App\Services;

use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ErrorMonitoringService
{
    private array $masked = ['password', 'password_confirmation', '_token'];

    public function capture(Throwable $exception, string $level = 'error'): void
    {
        ErrorLog::create([
            'level'       => $level,
            'message'     => $exception->getMessage(),
            'stack_trace' => $exception->getTraceAsString(),
            'context'     => [
                'url'    => optional(request())->fullUrl(),
                'method' => optional(request())->method(),
                'input'  => $this->sanitise(optional(request())->all() ?? []),
            ],
            'file'       => $exception->getFile(),
            'line'       => $exception->getLine(),
            'user_id'    => Auth::id(),
            'created_at' => now(),
        ]);

        Log::channel('daily')->{$level}($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }

    private function sanitise(array $data): array
    {
        foreach ($this->masked as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = '***';
            }
        }
        return $data;
    }
}
