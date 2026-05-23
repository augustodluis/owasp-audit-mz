<?php

return [
    'max_depth'    => (int) env('AUDIT_CRAWLER_MAX_DEPTH', 3),
    'max_pages'    => (int) env('AUDIT_CRAWLER_MAX_PAGES', 200),
    'http_timeout' => (int) env('AUDIT_HTTP_TIMEOUT', 15),
    'user_agent'   => env('AUDIT_USER_AGENT', 'OWASP-AUDIT-MZ/1.0'),

    'monitoring' => [
        'enabled' => (bool) env('INTERNAL_MONITORING_ENABLED', true),
        'level'   => env('INTERNAL_MONITORING_LEVEL', 'error'),
    ],

    'checks' => [
        App\Checks\SqlInjectionCheck::class,
        App\Checks\XssCheck::class,
        App\Checks\CsrfCheck::class,
        App\Checks\SecurityHeadersCheck::class,
        App\Checks\CookieFlagsCheck::class,
        App\Checks\DirectoryListingCheck::class,
        App\Checks\InfoDisclosureCheck::class,
        App\Checks\AuthenticationCheck::class,
    ],
];
