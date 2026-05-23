<?php

namespace App\Checks;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Endpoint;
use App\Models\Vulnerability;
use GuzzleHttp\Client;
use Throwable;

class SqlInjectionCheck implements VulnerabilityCheck
{
    private const PAYLOADS = [
        "'",
        "' OR 1=1--",
        "1' AND SLEEP(3)--",
    ];

    private const ERROR_PATTERNS = [
        '/SQL syntax/i',
        '/mysql_fetch/i',
        '/ODBC SQL/i',
        '/PostgreSQL ERROR/i',
        '/SQLite\.Exception/i',
        '/sql server/i',
        '/SQLITE_ERROR/i',
        '/unrecognized token/i',
    ];

    public function code(): string { return 'SQLI'; }
    public function name(): string { return 'Injeccao SQL'; }
    public function defaultSeverity(): string { return 'High'; }

    public function check(Endpoint $endpoint): array
    {
        $params = $endpoint->parameters ?? [];
        if (empty($params)) return [];

        $http = new Client(['timeout' => 12, 'http_errors' => false, 'verify' => false]);
        $url  = $endpoint->page->url;
        $hits = [];

        foreach (array_keys($params) as $param) {
            foreach (self::PAYLOADS as $payload) {
                $options = $endpoint->method === 'GET'
                    ? ['query' => [$param => $payload]]
                    : ['form_params' => [$param => $payload]];

                try {
                    $start    = microtime(true);
                    $response = $http->request($endpoint->method, $url, $options);
                    $elapsed  = microtime(true) - $start;
                    $body     = (string) $response->getBody();
                } catch (Throwable $e) {
                    continue;
                }

                $matched = null;
                foreach (self::ERROR_PATTERNS as $pattern) {
                    if (preg_match($pattern, $body, $m)) {
                        $matched = $m[0];
                        break;
                    }
                }
                $timeBased = str_contains($payload, 'SLEEP') && $elapsed > 2.5;

                if ($matched || $timeBased) {
                    $hits[] = new Vulnerability([
                        'name'           => "Injeccao SQL no parametro '{$param}'",
                        'owasp_category' => 'A03:2021 Injection',
                        'risk'           => 'High',
                        'confidence'     => $timeBased ? 'High' : 'Medium',
                        'description'    => "O parametro '{$param}' no endpoint {$endpoint->method} {$url} reage a payloads de SQL Injection. " .
                                            ($timeBased
                                                ? "Foi observada uma latencia de " . round($elapsed, 2) . " segundos com payload time-based (SLEEP(3)), o que indica execucao real de SQL pela base de dados."
                                                : "A resposta contem a assinatura de erro de SQL '{$matched}', revelando que o input e concatenado em SQL sem prepared statements."),
                        'evidence'       => "Pedido: {$endpoint->method} {$url}\nPayload: {$param}={$payload}\nLatencia: " . round($elapsed, 3) . "s\nResposta (excerto):\n" . substr(strip_tags($body), 0, 600),
                        'bad_example'    => "<?php\n; codigo vulneravel\n\$id = \$_GET['{$param}'];\n\$sql = \"SELECT * FROM produtos WHERE id = \" . \$id;\nmysqli_query(\$conn, \$sql);",
                        'good_example'   => "<?php\n; uso correcto com PDO e prepared statements\n\$stmt = \$pdo->prepare('SELECT * FROM produtos WHERE id = ?');\n\$stmt->execute([(int) \$_GET['{$param}']]);\n\$rows = \$stmt->fetchAll();",
                        'solution'       => 'Substituir concatenacao de strings por prepared statements (PDO ou Eloquent). Validar tipos e tamanhos do input. Aplicar principio de minimo privilegio na conta SQL.',
                        'reference'      => 'https://owasp.org/Top10/A03_2021-Injection/',
                        'cwe_id'         => 89,
                    ]);
                    continue 2;
                }
            }
        }
        return $hits;
    }
}
