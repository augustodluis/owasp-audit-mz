<?php

namespace App\Checks;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Endpoint;
use App\Models\Vulnerability;

class CsrfCheck implements VulnerabilityCheck
{
    public function code(): string { return 'CSRF'; }
    public function name(): string { return 'Ausencia de proteccao CSRF'; }
    public function defaultSeverity(): string { return 'Medium'; }

    public function check(Endpoint $endpoint): array
    {
        if ($endpoint->method !== 'POST') return [];

        $params = array_keys($endpoint->parameters ?? []);
        foreach ($params as $param) {
            $lower = strtolower($param);
            if (str_contains($lower, 'csrf') || str_contains($lower, 'token') || $param === '_token') {
                return [];
            }
        }

        return [new Vulnerability([
            'name'           => 'Formulario POST sem token anti-CSRF',
            'owasp_category' => 'A01:2021 Broken Access Control',
            'risk'           => 'Medium',
            'confidence'     => 'Medium',
            'description'    => "O formulario em {$endpoint->page->url} (metodo POST) nao contem campo identificavel como token anti-CSRF. Um atacante pode forjar pedidos cross-site em nome da vitima autenticada.",
            'evidence'       => "URL: {$endpoint->page->url}\nMetodo: POST\nParametros detectados: " . (empty($params) ? '(nenhum)' : implode(', ', $params)) . "\nNenhum campo csrf/token/_token encontrado.",
            'bad_example'    => "<form method=\"POST\" action=\"/transferir\">\n  <input name=\"para\">\n  <input name=\"valor\">\n  <button>Enviar</button>\n</form>",
            'good_example'   => "<form method=\"POST\" action=\"/transferir\">\n  @csrf\n  <input name=\"para\">\n  <input name=\"valor\">\n  <button>Enviar</button>\n</form>\n\n; Express usa o pacote csurf:\napp.use(csrf());\n; renderiza <input type=\"hidden\" name=\"_csrf\" value=\"{{token}}\">",
            'solution'       => 'Em Laravel, usar @csrf nos formularios Blade e manter VerifyCsrfToken middleware activo. Em Express/Node, instalar csurf. Em APIs, exigir cabecalho personalizado (ex: X-Requested-With) e configurar SameSite=Strict nos cookies de sessao.',
            'reference'      => 'https://owasp.org/www-community/attacks/csrf',
            'cwe_id'         => 352,
        ])];
    }
}
