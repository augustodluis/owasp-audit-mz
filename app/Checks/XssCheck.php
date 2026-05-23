<?php

namespace App\Checks;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Endpoint;
use App\Models\Vulnerability;
use GuzzleHttp\Client;
use Throwable;

class XssCheck implements VulnerabilityCheck
{
    public function code(): string { return 'XSS'; }
    public function name(): string { return 'Cross-Site Scripting'; }
    public function defaultSeverity(): string { return 'High'; }

    public function check(Endpoint $endpoint): array
    {
        $params = $endpoint->parameters ?? [];
        if (empty($params)) return [];

        $marker  = 'audmz' . bin2hex(random_bytes(4));
        $payload = "<script>/*{$marker}*/</script>";
        $http    = new Client(['timeout' => 10, 'http_errors' => false, 'verify' => false]);
        $url     = $endpoint->page->url;
        $hits    = [];

        foreach (array_keys($params) as $param) {
            $options = $endpoint->method === 'GET'
                ? ['query' => [$param => $payload]]
                : ['form_params' => [$param => $payload]];

            try {
                $body = (string) $http->request($endpoint->method, $url, $options)->getBody();
            } catch (Throwable $e) {
                continue;
            }

            if (str_contains($body, $payload) || str_contains($body, $marker)) {
                $pos     = max(0, strpos($body, $marker) - 100);
                $excerpt = substr($body, $pos, 320);

                $hits[] = new Vulnerability([
                    'name'           => "Reflexao de XSS no parametro '{$param}'",
                    'owasp_category' => 'A03:2021 Injection',
                    'risk'           => 'High',
                    'confidence'     => 'Medium',
                    'description'    => "O parametro '{$param}' no endpoint {$endpoint->method} {$url} reflecte HTML sem qualquer escape. Um atacante pode injectar <script> que executa no navegador da vitima.",
                    'evidence'       => "Pedido: {$endpoint->method} {$url}\nPayload: {$param}={$payload}\nMarcador: {$marker}\nResposta (excerto da reflexao):\n" . trim($excerpt),
                    'bad_example'    => "<?php\n; codigo vulneravel\necho \"<p>Pesquisa: \" . \$_GET['{$param}'] . \"</p>\";",
                    'good_example'   => "<?php\n; em PHP puro\necho '<p>Pesquisa: ' . htmlspecialchars(\$_GET['{$param}'], ENT_QUOTES, 'UTF-8') . '</p>';\n\n; em Laravel Blade\n<p>Pesquisa: {{ \$query }}</p>  // escape automatico\n; NUNCA: {!! \$query !!}",
                    'solution'       => 'Aplicar escape HTML na renderizacao. Usar {{ }} em Blade ou htmlspecialchars() em PHP puro. Validar input no servidor. Definir Content-Security-Policy estrita.',
                    'reference'      => 'https://owasp.org/Top10/A03_2021-Injection/',
                    'cwe_id'         => 79,
                ]);
            }
        }
        return $hits;
    }
}
