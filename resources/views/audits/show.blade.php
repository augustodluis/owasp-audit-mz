@extends('layouts.app')
@section('title', 'Auditoria #' . $audit->id)
@section('content')
@php $totals = $audit->totals(); @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Auditoria #{{ $audit->id }}</h2>
    <div>
        <a href="{{ route('audits.report', $audit) }}" class="btn btn-outline-dark">Ver relatorio</a>
        <a href="{{ route('audits.report.pdf', $audit) }}" class="btn btn-outline-dark">Exportar PDF</a>
        <a href="{{ route('audits.report.csv', $audit) }}" class="btn btn-outline-dark">Exportar CSV</a>
        @if ($previous)
            <a href="{{ route('audits.compare', $audit) }}" class="btn btn-outline-secondary">Comparar com #{{ $previous->id }}</a>
        @endif
        @if (! empty($audit->recipients))
            <form method="POST" action="{{ route('audits.resend', $audit) }}" class="d-inline">
                @csrf
                <button class="btn btn-outline-primary">Reenviar relatorio</button>
            </form>
        @endif
        <form method="POST" action="{{ route('audits.destroy', $audit) }}" class="d-inline"
              onsubmit="return confirm('Eliminar definitivamente?');">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger">Eliminar</button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-danger"><div class="card-body">
        <small class="text-muted">Alto</small><h3 class="text-danger">{{ $totals['High'] }}</h3>
    </div></div></div>
    <div class="col-md-3"><div class="card border-warning"><div class="card-body">
        <small class="text-muted">Medio</small><h3 class="text-warning">{{ $totals['Medium'] }}</h3>
    </div></div></div>
    <div class="col-md-3"><div class="card border-info"><div class="card-body">
        <small class="text-muted">Baixo</small><h3 class="text-info">{{ $totals['Low'] }}</h3>
    </div></div></div>
    <div class="col-md-3"><div class="card border-secondary"><div class="card-body">
        <small class="text-muted">Informativo</small><h3>{{ $totals['Informational'] }}</h3>
    </div></div></div>
</div>

<dl class="row mb-4">
    <dt class="col-sm-3">Alvo</dt><dd class="col-sm-9"><code>{{ $audit->target_url }}</code></dd>
    <dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $audit->status }}</dd>
    <dt class="col-sm-3">Inicio</dt><dd class="col-sm-9">{{ $audit->started_at?->format('Y-m-d H:i:s') }}</dd>
    <dt class="col-sm-3">Fim</dt><dd class="col-sm-9">{{ $audit->finished_at?->format('Y-m-d H:i:s') }}</dd>
    <dt class="col-sm-3">Paginas descobertas</dt><dd class="col-sm-9">{{ $audit->pages->count() }}</dd>
    <dt class="col-sm-3">Destinatarios</dt>
    <dd class="col-sm-9">
        @if (! empty($audit->recipients))
            {{ implode(', ', $audit->recipients) }}
            @if ($audit->email_sent)
                <span class="badge text-bg-success ms-2">enviado</span>
            @else
                <span class="badge text-bg-secondary ms-2">por enviar</span>
            @endif
        @else
            <span class="text-muted">nenhum</span>
        @endif
    </dd>
</dl>
@endsection
