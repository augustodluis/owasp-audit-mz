<?php

namespace App\Checks;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Endpoint;
use App\Models\Vulnerability;
use GuzzleHttp\Client;
use Throwable;

class DirectoryListingCheck implements VulnerabilityCheck
{
    private array $paths = ['/admin/', '/backup/', '/uploads/', '/files/', '/storage/', '/ftp/'];

    public function code(): string { return 'DIRLIST'; }
    public function name(): string { return 'Directory listing exposto'; }
    public function defaultSeverity(): string { return 'Medium'; }

    public function check(Endpoint $endpoint): array
    {
        static $tested = [];
        $scheme = parse_url($endpoint->page->url, PHP_URL_SCHEME);
        $host   = parse_url($endpoint->page->url, PHP_URL_HOST);
        $port   = parse_url($endpoint->page->url, PHP_URL_PORT);
        $base   = "{$scheme}://{$host}" . ($port ? ":{$port}" : '');
        if (isset($tested[$base])) return [];
        $tested[$base] = true;

        $http = new Client(['timeout' => 8, 'http_errors' => false, 'verify' => false]);
        $hits = [];

        foreach ($this->paths as $path) {
            try {
                $response = $http->get($base . $path);
                $body     = (string) $response->getBody();
                $status   = $response->getStatusCode();
            } catch (Throwable $e) {
                continue;
            }

            $isDir = preg_match('/Index of \//i', $body)
                  || str_contains($body, '<title>Index of')
                  || (str_contains(strtolower($body), '<a href="') && str_contains(strtolower($body), 'last modified'));

            if ($isDir) {
                $hits[] = new Vulnerability([
                    'name'           => "Listagem de directorio exposta em {$path}",
                    'owasp_category' => 'A05:2021 Security Misconfiguration',
                    'risk'           => 'Medium',
                    'confidence'     => 'High',
                    'description'    => "O caminho {$base}{$path} respondeu HTTP {$status} com listagem de ficheiros visivel. Atacantes podem enumerar backups, codigo-fonte ou credenciais.",
                    'evidence'       => "Pedido: GET {$base}{$path}\nResposta (excerto):\n" . substr(strip_tags($body), 0, 800),
                    'bad_example'    => "# Apache, configuracao actual permite listagem\nOptions +Indexes",
                    'good_example'   => "# Apache: desactivar indexes\nOptions -Indexes\n\n# Nginx\nautoindex off;\n\n# Acrescentar index.html ou index.php vazio no directorio",
                    'solution'       => 'Desactivar a directiva de indexacao automatica no servidor web e colocar um index.html vazio em pastas que nao devam servir ficheiros directamente.',
                    'reference'      => 'https://owasp.org/www-community/attacks/Forced_browsing',
                    'cwe_id'         => 548,
                ]);
            }
        }
        return $hits;
    }
}
