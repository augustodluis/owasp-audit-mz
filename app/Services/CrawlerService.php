<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Endpoint;
use App\Models\Page;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class CrawlerService
{
    private Client $http;
    private int $maxDepth;
    private int $maxPages;

    private const COMMON_PATHS = [
        '/robots.txt',
        '/sitemap.xml',
        '/.git/HEAD',
        '/ftp',
        '/admin/',
        '/api/Users/',
        '/api/Users',
        '/api/Feedbacks/',
        '/api/BasketItems/',
        '/api/Quantitys/',
        '/rest/user/login',
        '/rest/user/whoami',
        '/rest/products/search',
        '/rest/admin/application-version',
        '/rest/admin/application-configuration',
        '/rest/captcha',
        '/rest/languages',
    ];

    private const PROBE_PARAMS = [
        '/rest/products/search' => ['q' => 'text'],
        '/rest/user/login'      => ['email' => 'text', 'password' => 'password'],
    ];

    public function __construct()
    {
        $this->http = new Client([
            'timeout'         => (int) config('audit.http_timeout', 15),
            'verify'          => false,
            'headers'         => ['User-Agent' => config('audit.user_agent', 'OWASP-AUDIT-MZ/1.0')],
            'allow_redirects' => true,
            'http_errors'     => false,
        ]);
        $this->maxDepth = (int) config('audit.max_depth', 3);
        $this->maxPages = (int) config('audit.max_pages', 200);
    }

    public function discover(Audit $audit): void
    {
        $start = $audit->target_url;
        $base  = parse_url($start, PHP_URL_HOST);
        $seen  = [];

        $this->indexPage($audit, $start);
        $seen[$start] = true;

        $queue = [[$start, 0]];
        while ($queue && count($seen) < $this->maxPages) {
            [$url, $depth] = array_shift($queue);
            $dom = $this->fetchHtml($url);
            if (! $dom) continue;

            $dom->filter('a[href]')->each(function (Crawler $link) use (&$queue, $depth, $base, &$seen, $audit) {
                try {
                    $href = $link->link()->getUri();
                } catch (Throwable $e) {
                    return;
                }
                if (parse_url($href, PHP_URL_HOST) !== $base) return;
                if (isset($seen[$href]) || count($seen) >= $this->maxPages) return;
                $seen[$href] = true;
                $this->indexPage($audit, $href);
                if ($depth + 1 < $this->maxDepth) {
                    $queue[] = [$href, $depth + 1];
                }
            });
        }

        $this->probeCommonPaths($audit, $start, $seen);
    }

    private function indexPage(Audit $audit, string $url): void
    {
        $response = $this->safeGet($url);
        if (! $response) return;

        $page = Page::create([
            'audit_id'      => $audit->id,
            'url'           => $url,
            'http_status'   => $response->getStatusCode(),
            'discovered_at' => now(),
        ]);

        Endpoint::create([
            'page_id'    => $page->id,
            'method'     => 'GET',
            'parameters' => [],
        ]);

        $body = (string) $response->getBody();
        if ($body !== '' && stripos($response->getHeaderLine('Content-Type'), 'html') !== false) {
            try {
                $dom = new Crawler($body, $url);
                $dom->filter('form')->each(function (Crawler $form) use ($page) {
                    Endpoint::create([
                        'page_id'    => $page->id,
                        'method'     => strtoupper($form->attr('method') ?? 'GET'),
                        'parameters' => $this->extractFields($form),
                    ]);
                });
            } catch (Throwable $e) {}
        }
    }

    private function fetchHtml(string $url): ?Crawler
    {
        $response = $this->safeGet($url);
        if (! $response) return null;
        $body = (string) $response->getBody();
        if ($body === '') return null;
        if (stripos($response->getHeaderLine('Content-Type'), 'html') === false) return null;
        try {
            return new Crawler($body, $url);
        } catch (Throwable $e) {
            return null;
        }
    }

    private function probeCommonPaths(Audit $audit, string $start, array &$seen): void
    {
        $scheme = parse_url($start, PHP_URL_SCHEME);
        $host   = parse_url($start, PHP_URL_HOST);
        $port   = parse_url($start, PHP_URL_PORT);
        $base   = "{$scheme}://{$host}" . ($port ? ":{$port}" : '');

        foreach (self::COMMON_PATHS as $path) {
            if (count($seen) >= $this->maxPages) return;
            $url = $base . $path;
            if (isset($seen[$url])) continue;

            $response = $this->safeGet($url);
            if (! $response) continue;
            $status = $response->getStatusCode();
            if ($status >= 500) continue;

            $seen[$url] = true;
            $page = Page::create([
                'audit_id'      => $audit->id,
                'url'           => $url,
                'http_status'   => $status,
                'discovered_at' => now(),
            ]);

            $params = self::PROBE_PARAMS[$path] ?? [];
            Endpoint::create([
                'page_id'    => $page->id,
                'method'     => 'GET',
                'parameters' => $params,
            ]);
        }
    }

    private function safeGet(string $url): ?Response
    {
        try {
            $response = $this->http->request('GET', $url);
            return $response instanceof Response ? $response : new Response(
                $response->getStatusCode(),
                $response->getHeaders(),
                (string) $response->getBody()
            );
        } catch (Throwable $e) {
            return null;
        }
    }

    private function extractFields(Crawler $form): array
    {
        $fields = [];
        $form->filter('input, textarea, select')->each(function (Crawler $field) use (&$fields) {
            $name = $field->attr('name');
            if ($name) {
                $fields[$name] = $field->attr('type') ?? 'text';
            }
        });
        return $fields;
    }
}
