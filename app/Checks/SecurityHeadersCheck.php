<?php

namespace App\Checks;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Endpoint;
use App\Models\Vulnerability;
use GuzzleHttp\Client;
use Throwable;

class SecurityHeadersCheck implements VulnerabilityCheck
{
    private array $required = [
        'Content-Security-Policy' => [
            'risk' => 'High',
            'cwe'  => 693,
            'why'  => 'Sem CSP, o navegador executa scripts de qualquer origem, abrindo porta a XSS armazenado e exfiltracao.',
            'bad'  => "// Resposta HTTP atual\nHTTP/1.1 200 OK\nContent-Type: text/html\n; cabecalho CSP ausente",
            'good' => "// Apache: .htaccess ou httpd.conf\nHeader set Content-Security-Policy \"default-src 'self'; script-src 'self'\"",
        ],
        'Strict-Transport-Security' => [
            'risk' => 'Medium',
            'cwe'  => 319,
            'why'  => 'HSTS forca o navegador a usar HTTPS, evitando ataques de downgrade e SSL stripping.',
            'bad'  => "HTTP/1.1 200 OK\n; sem Strict-Transport-Security",
            'good' => "Header set Strict-Transport-Security \"max-age=31536000; includeSubDomains; preload\"",
        ],
        'X-Frame-Options' => [
            'risk' => 'Medium',
            'cwe'  => 1021,
            'why'  => 'Sem X-Frame-Options, a pagina pode ser carregada em iframe num site malicioso (clickjacking).',
            'bad'  => "HTTP/1.1 200 OK\n; sem X-Frame-Options",
            'good' => "Header always set X-Frame-Options \"SAMEORIGIN\"",
        ],
        'X-Content-Type-Options' => [
            'risk' => 'Low',
            'cwe'  => 16,
            'why'  => 'Sem este cabecalho, o navegador pode adivinhar o MIME e tratar texto como script (MIME sniffing).',
            'bad'  => "HTTP/1.1 200 OK\n; sem X-Content-Type-Options",
            'good' => "Header set X-Content-Type-Options \"nosniff\"",
        ],
        'Referrer-Policy' => [
            'risk' => 'Low',
            'cwe'  => 200,
            'why'  => 'Sem Referrer-Policy, URLs com tokens podem fugir no cabecalho Referer.',
            'bad'  => "HTTP/1.1 200 OK\n; sem Referrer-Policy",
            'good' => "Header set Referrer-Policy \"strict-origin-when-cross-origin\"",
        ],
    ];

    public function code(): string { return 'SECHEAD'; }
    public function name(): string { return 'Cabecalhos de seguranca em falta'; }
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

        $present = [];
        foreach ($response->getHeaders() as $name => $values) {
            $present[strtolower($name)] = implode(', ', (array) $values);
        }

        $hits = [];
        foreach ($this->required as $header => $meta) {
            if (! isset($present[strtolower($header)])) {
                $hits[] = new Vulnerability([
                    'name'           => "Cabecalho de seguranca em falta: {$header}",
                    'owasp_category' => 'A05:2021 Security Misconfiguration',
                    'risk'           => $meta['risk'],
                    'confidence'     => 'High',
                    'description'    => "A resposta de {$url} nao inclui o cabecalho {$header}. {$meta['why']}",
                    'evidence'       => "Pedido: GET {$url}\nCabecalhos recebidos:\n" . substr(json_encode($present, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), 0, 1500),
                    'bad_example'    => $meta['bad'],
                    'good_example'   => $meta['good'],
                    'solution'       => "Adicionar o cabecalho {$header} na resposta do servidor web ou via middleware da aplicacao.",
                    'reference'      => 'https://owasp.org/www-project-secure-headers/',
                    'cwe_id'         => $meta['cwe'],
                ]);
            }
        }
        return $hits;
    }
}
