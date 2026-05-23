<?php

namespace App\Checks;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Endpoint;
use App\Models\Vulnerability;
use GuzzleHttp\Client;
use Throwable;

class InfoDisclosureCheck implements VulnerabilityCheck
{
    private array $headers = ['Server', 'X-Powered-By', 'X-AspNet-Version'];

    public function code(): string { return 'INFODISC'; }
    public function name(): string { return 'Exposicao de informacao tecnica'; }
    public function defaultSeverity(): string { return 'Low'; }

    public function check(Endpoint $endpoint): array
    {
        static $tested = [];
        $url = $endpoint->page->url;
        if (isset($tested[$url])) return [];
        $tested[$url] = true;

        try {
            $response = (new Client(['timeout' => 8, 'http_errors' => false, 'verify' => false]))->get($url);
        } catch (Throwable $e) {
            return [];
        }

        $hits = [];
        foreach ($this->headers as $header) {
            $value = $response->getHeaderLine($header);
            if ($value !== '') {
                $hits[] = new Vulnerability([
                    'name'           => "Cabecalho informativo: {$header}: {$value}",
                    'owasp_category' => 'A05:2021 Security Misconfiguration',
                    'risk'           => 'Low',
                    'confidence'     => 'High',
                    'description'    => "O cabecalho {$header} revela a tecnologia ({$value}) usada pelo servidor. Um atacante pode procurar CVEs especificos para essa versao.",
                    'evidence'       => "Pedido: GET {$url}\nResposta inclui: {$header}: {$value}",
                    'bad_example'    => "HTTP/1.1 200 OK\n{$header}: {$value}",
                    'good_example'   => "// Apache: httpd.conf\nServerTokens Prod\nServerSignature Off\n\n// Express (Node)\napp.disable('x-powered-by');\n\n// Nginx\nserver_tokens off;",
                    'solution'       => 'Remover ou anonimizar o cabecalho na configuracao do servidor web ou da framework.',
                    'reference'      => 'https://owasp.org/www-community/Improper_Error_Handling',
                    'cwe_id'         => 200,
                ]);
            }
        }
        return $hits;
    }
}
