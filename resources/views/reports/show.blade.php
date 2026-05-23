@extends('layouts.app')
@section('title', 'Relatorio #' . $audit->id)
@section('content')
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
    $totals = $audit->totals();

    $riskBadge = [
        'High'          => 'danger',
        'Medium'        => 'warning',
        'Low'           => 'info',
        'Informational' => 'secondary',
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-1">Relatorio de Auditoria #{{ $audit->id }}</h2>
        <small class="text-muted">Alvo: <code>{{ $audit->target_url }}</code></small>
    </div>
    <div>
        <a href="{{ route('audits.show', $audit) }}" class="btn btn-outline-dark">Voltar</a>
        <a href="{{ route('audits.report.pdf', $audit) }}" class="btn btn-outline-dark">Exportar PDF</a>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach (['High'=>'Alto','Medium'=>'Medio','Low'=>'Baixo','Informational'=>'Informativo'] as $k => $label)
        <div class="col-md-3">
            <div class="card border-{{ $riskBadge[$k] }}">
                <div class="card-body">
                    <small class="text-muted">{{ $label }}</small>
                    <h3 class="text-{{ $riskBadge[$k] }}">{{ $totals[$k] }}</h3>
                </div>
            </div>
        </div>
    @endforeach
</div>

<dl class="row mb-4 small">
    <dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $audit->status }}</dd>
    <dt class="col-sm-3">Inicio</dt><dd class="col-sm-9">{{ $audit->started_at?->format('Y-m-d H:i:s') }}</dd>
    <dt class="col-sm-3">Fim</dt><dd class="col-sm-9">{{ $audit->finished_at?->format('Y-m-d H:i:s') }}</dd>
    <dt class="col-sm-3">Paginas descobertas</dt><dd class="col-sm-9">{{ $audit->pages->count() }}</dd>
    <dt class="col-sm-3">Total de vulnerabilidades</dt>
    <dd class="col-sm-9">{{ array_sum($totals) }}</dd>
</dl>

<h4 class="mt-4 mb-3">Detalhe por categoria OWASP Top 10</h4>

@foreach ($byCategory as $category => $items)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <strong>{{ $category }}</strong>
            <span class="badge text-bg-light ms-2">{{ count($items) }}</span>
        </div>
        <div class="card-body p-0">
            @foreach ($items as $v)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge text-bg-{{ $riskBadge[$v->risk] }} me-2">{{ $v->risk }}</span>
                            <strong>{{ $v->name }}</strong>
                        </div>
                        <small class="text-muted">{{ $v->check_code }} | CWE-{{ $v->cwe_id }} | confianca: {{ $v->confidence }}</small>
                    </div>

                    <p class="mt-2 mb-2">{{ $v->description }}</p>

                    <form method="POST" action="{{ route('vulnerabilities.update', $v) }}" class="row g-2 mb-2">
                        @csrf @method('PATCH')
                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-sm">
                                <option value="open"           @selected($v->status === 'open')>Em aberto</option>
                                <option value="accepted"       @selected($v->status === 'accepted')>Aceite (risco assumido)</option>
                                <option value="false_positive" @selected($v->status === 'false_positive')>Falso positivo</option>
                                <option value="fixed"          @selected($v->status === 'fixed')>Corrigido</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="notes" value="{{ $v->notes }}"
                                   placeholder="Notas internas (opcional)"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-outline-dark w-100">Guardar</button>
                        </div>
                    </form>

                    @if ($v->evidence)
                        <div class="mb-2">
                            <small class="text-muted d-block">Evidencia tecnica</small>
                            <pre class="bg-light p-2 small mb-0" style="white-space:pre-wrap;">{{ $v->evidence }}</pre>
                        </div>
                    @endif

                    @if ($v->bad_example || $v->good_example)
                        <div class="row g-2 mt-2">
                            @if ($v->bad_example)
                                <div class="col-md-6">
                                    <small class="text-danger d-block">Codigo vulneravel</small>
                                    <pre class="bg-light p-2 small mb-0" style="white-space:pre-wrap; border-left:3px solid #C8102E;">{{ $v->bad_example }}</pre>
                                </div>
                            @endif
                            @if ($v->good_example)
                                <div class="col-md-6">
                                    <small class="text-success d-block">Correccao recomendada</small>
                                    <pre class="bg-light p-2 small mb-0" style="white-space:pre-wrap; border-left:3px solid #198754;">{{ $v->good_example }}</pre>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($v->solution)
                        <div class="mt-2">
                            <small class="text-muted d-block">Como mitigar</small>
                            <p class="mb-1 small">{{ $v->solution }}</p>
                        </div>
                    @endif

                    <small>
                        @if ($v->reference)
                            <a href="{{ $v->reference }}" target="_blank" rel="noopener">{{ $v->reference }}</a>
                        @endif
                    </small>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
@endsection
