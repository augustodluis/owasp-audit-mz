<?php

namespace App\Checks;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Endpoint;
use App\Models\Vulnerability;
use GuzzleHttp\Client;
use Throwable;

class CookieFlagsCheck implements VulnerabilityCheck
{
    public function code(): string { return 'COOKIE'; }
    public function name(): string { return 'Cookies inseguros'; }
    public function defaultSeverity(): string { return 'Medium'; }

    public function check(Endpoint $endpoint): array
    {
        static $tested = [];
        $url = $endpoint->page->url;
        if (isset($tested[$url])) return [];
        $tested[$url] = true;

        try {
            $response = (new Client(['timeout' => 10, 'http_errors' => false, 'verify' => false]))->get($url);
        } catch (Throwable $e) {
            return [];
        }

        $hits = [];
        foreach ($response->getHeader('Set-Cookie') as $cookieHeader) {
            $lower   = strtolower($cookieHeader);
            $name    = explode('=', $cookieHeader, 2)[0];
            $missing = [];
            if (! str_contains($lower, 'secure'))   $missing[] = 'Secure';
            if (! str_contains($lower, 'httponly')) $missing[] = 'HttpOnly';
            if (! str_contains($lower, 'samesite')) $missing[] = 'SameSite';

            if ($missing) {
                $hits[] = new Vulnerability([
                    'name'           => "Cookie {$name} sem flags: " . implode(', ', $missing),
                    'owasp_category' => 'A05:2021 Security Misconfiguration',
                    'risk'           => 'Medium',
                    'confidence'     => 'High',
                    'description'    => "O cookie {$name} foi enviado sem as flags " . implode(', ', $missing) . ". Cookies sem HttpOnly podem ser lidos por JavaScript (potencial XSS); sem Secure transitam em HTTP claro; sem SameSite ficam expostos a CSRF cross-site.",
                    'evidence'       => "Pedido: GET {$url}\nSet-Cookie recebido:\n{$cookieHeader}",
                    'bad_example'    => "Set-Cookie: {$name}=abc123; Path=/",
                    'good_example'   => "Set-Cookie: {$name}=abc123; Path=/; Secure; HttpOnly; SameSite=Lax\n\n// Em Laravel, no config/session.php\n'secure'    => true,\n'http_only' => true,\n'same_site' => 'lax',",
                    'solution'       => 'Em Laravel, ajustar config/session.php. Em Express/Node, options { secure:true, httpOnly:true, sameSite:"lax" }. Para outros servidores, configurar no middleware ou no proxy reverso.',
                    'reference'      => 'https://owasp.org/www-community/HttpOnly',
                    'cwe_id'         => 614,
                ]);
            }
        }
        return $hits;
    }
}
