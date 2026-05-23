<?php

namespace App\Checks;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Endpoint;
use App\Models\Vulnerability;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class AuthenticationCheck implements VulnerabilityCheck
{
    public function code(): string { return 'AUTH'; }
    public function name(): string { return 'Falhas de autenticacao'; }
    public function defaultSeverity(): string { return 'Medium'; }

    public function check(Endpoint $endpoint): array
    {
        static $tested = [];
        $url = $endpoint->page->url;
        if (isset($tested[$url])) return [];
        $tested[$url] = true;

        try {
            $body = (string) (new Client(['timeout' => 8, 'http_errors' => false, 'verify' => false]))->get($url)->getBody();
        } catch (Throwable $e) {
            return [];
        }
        if ($body === '') return [];

        try {
            $dom = new Crawler($body, $url);
        } catch (Throwable $e) {
            return [];
        }

        $passwordFields = $dom->filter('input[type=password]');
        if ($passwordFields->count() === 0) return [];

        $hits = [];

        if (! str_starts_with($url, 'https://')) {
            $hits[] = new Vulnerability([
                'name'           => 'Formulario de palavra-passe servido sem HTTPS',
                'owasp_category' => 'A02:2021 Cryptographic Failures',
                'risk'           => 'High',
                'confidence'     => 'High',
                'description'    => "A pagina {$url} contem campos de palavra-passe mas e servida em HTTP. As credenciais transitam em claro entre o navegador e o servidor e podem ser interceptadas em redes nao confiaveis.",
                'evidence'       => "URL auditada: {$url}\nEsquema: " . parse_url($url, PHP_URL_SCHEME) . "\nFoi encontrado pelo menos um campo input[type=password] no HTML.",
                'bad_example'    => "<form method=\"POST\" action=\"http://exemplo.com/login\">\n  <input type=\"password\" name=\"password\">\n</form>",
                'good_example'   => "<form method=\"POST\" action=\"https://exemplo.com/login\">\n  <input type=\"password\" name=\"password\" autocomplete=\"off\">\n</form>\n\n; Forcar HTTPS em Laravel: AppServiceProvider::boot()\nURL::forceScheme('https');",
                'solution'       => 'Instalar certificado TLS valido, redireccionar HTTP para HTTPS e forcar HSTS.',
                'reference'      => 'https://owasp.org/Top10/A02_2021-Cryptographic_Failures/',
                'cwe_id'         => 319,
            ]);
        }

        $passwordFields->each(function (Crawler $field) use (&$hits, $url) {
            if (strtolower($field->attr('autocomplete') ?? '') !== 'off') {
                $hits[] = new Vulnerability([
                    'name'           => "Campo password sem autocomplete=off em {$url}",
                    'owasp_category' => 'A07:2021 Identification and Authentication Failures',
                    'risk'           => 'Low',
                    'confidence'     => 'Medium',
                    'description'    => 'O input type=password nao declara autocomplete=off. Em ambientes partilhados, o navegador pode preencher a password automaticamente, facilitando credential stuffing.',
                    'evidence'       => "URL: {$url}\nHTML do campo:\n" . trim($field->outerHtml()),
                    'bad_example'    => "<input type=\"password\" name=\"password\">",
                    'good_example'   => "<input type=\"password\" name=\"password\" autocomplete=\"off\" required>",
                    'solution'       => 'Adicionar autocomplete="off" ou autocomplete="new-password" no input password.',
                    'reference'      => 'https://owasp.org/www-community/attacks/Credential_stuffing',
                    'cwe_id'         => 522,
                ]);
            }
        });

        return $hits;
    }
}
