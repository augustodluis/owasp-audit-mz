<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatorio auditoria {{ $audit->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Helvetica, sans-serif; font-size: 10pt; color: #2D2D2D; }
        h1 { color: #C8102E; font-size: 18pt; margin: 0 0 8px; }
        h2 { color: #2D2D2D; font-size: 13pt; margin: 16px 0 6px; border-bottom: 2px solid #C8102E; padding-bottom: 3px; }
        h3 { font-size: 11pt; margin: 10px 0 2px; }
        .meta { background:#f6f6f6; padding:8px; margin: 4px 0 12px; }
        .vuln { margin: 4px 0 10px; padding: 8px; border: 1px solid #ddd; page-break-inside: avoid; }
        .high   { border-left: 4px solid #C8102E; }
        .medium { border-left: 4px solid #f0ad4e; }
        .low    { border-left: 4px solid #5bc0de; }
        .info   { border-left: 4px solid #999; }
        pre { background:#f8f8f8; padding:6px; font-size: 8.5pt; white-space: pre-wrap; margin: 4px 0; }
        .bad  { border-left: 3px solid #C8102E; }
        .good { border-left: 3px solid #198754; }
        small { color: #666; font-size: 8pt; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; color: #fff; font-size: 8pt; }
        .badge-high { background:#C8102E; }
        .badge-medium { background:#f0ad4e; }
        .badge-low { background:#5bc0de; }
        .badge-info { background:#999; }
    </style>
</head>
<body>

<h1>Relatorio de Auditoria de Seguranca</h1>

<div class="meta">
    <strong>ID:</strong> {{ $audit->id }}<br>
    <strong>Alvo:</strong> {{ $audit->target_url }}<br>
    <strong>Inicio:</strong> {{ $audit->started_at }}<br>
    <strong>Fim:</strong> {{ $audit->finished_at }}<br>
    <strong>Paginas descobertas:</strong> {{ $audit->pages->count() }}
</div>

@php
    $groups = ['High'=>[], 'Medium'=>[], 'Low'=>[], 'Informational'=>[]];
    foreach ($audit->pages as $page) {
        foreach ($page->endpoints as $endpoint) {
            foreach ($endpoint->vulnerabilities as $vulnerability) {
                $groups[$vulnerability->risk][] = $vulnerability;
            }
        }
    }
    $byCategory = [];
    foreach ($groups as $list) {
        foreach ($list as $v) {
            $cat = $v->owasp_category ?: 'Outras';
            $byCategory[$cat][] = $v;
        }
    }
    ksort($byCategory);
    $cls = ['High'=>'high','Medium'=>'medium','Low'=>'low','Informational'=>'info'];
    $bcls = ['High'=>'badge-high','Medium'=>'badge-medium','Low'=>'badge-low','Informational'=>'badge-info'];
    $totals = $audit->totals();
@endphp

<h2>Sumario</h2>
<p>
    <span class="badge badge-high">Alto: {{ $totals['High'] }}</span>
    <span class="badge badge-medium">Medio: {{ $totals['Medium'] }}</span>
    <span class="badge badge-low">Baixo: {{ $totals['Low'] }}</span>
    <span class="badge badge-info">Informativo: {{ $totals['Informational'] }}</span>
</p>

@foreach ($byCategory as $category => $items)
    <h2>{{ $category }} <small>({{ count($items) }})</small></h2>
    @foreach ($items as $v)
        <div class="vuln {{ $cls[$v->risk] }}">
            <h3>
                <span class="badge {{ $bcls[$v->risk] }}">{{ $v->risk }}</span>
                {{ $v->name }}
            </h3>
            <small>{{ $v->check_code }} | CWE-{{ $v->cwe_id }} | confianca: {{ $v->confidence }}</small>
            <p>{{ $v->description }}</p>

            @if ($v->evidence)
                <small><strong>Evidencia tecnica:</strong></small>
                <pre>{{ $v->evidence }}</pre>
            @endif

            @if ($v->bad_example)
                <small><strong>Codigo vulneravel:</strong></small>
                <pre class="bad">{{ $v->bad_example }}</pre>
            @endif

            @if ($v->good_example)
                <small><strong>Correccao recomendada:</strong></small>
                <pre class="good">{{ $v->good_example }}</pre>
            @endif

            @if ($v->solution)
                <small><strong>Como mitigar:</strong> {{ $v->solution }}</small><br>
            @endif

            @if ($v->reference)
                <small>Referencia: {{ $v->reference }}</small>
            @endif
        </div>
    @endforeach
@endforeach

</body>
</html>
