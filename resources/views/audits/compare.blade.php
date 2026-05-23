@extends('layouts.app')
@section('title', 'Comparar #' . $current->id . ' vs #' . $previous->id)
@section('content')

<h2 class="mb-3">Comparacao de auditorias</h2>
<p class="text-muted">Alvo: <code>{{ $current->target_url }}</code></p>

<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th>Risco</th>
            <th class="text-center">Anterior (#{{ $previous->id }})<br><small class="text-muted">{{ $previous->finished_at?->format('Y-m-d H:i') }}</small></th>
            <th class="text-center">Actual (#{{ $current->id }})<br><small class="text-muted">{{ $current->finished_at?->format('Y-m-d H:i') }}</small></th>
            <th class="text-center">Variacao</th>
        </tr>
    </thead>
    <tbody>
        @foreach (['High'=>'Alto','Medium'=>'Medio','Low'=>'Baixo','Informational'=>'Informativo'] as $k => $label)
            @php
                $prev   = $previousTotals[$k];
                $now    = $currentTotals[$k];
                $delta  = $now - $prev;
                $dClass = $delta < 0 ? 'text-success' : ($delta > 0 ? 'text-danger' : 'text-muted');
                $dSign  = $delta > 0 ? '+' . $delta : ($delta === 0 ? '0' : (string) $delta);
            @endphp
            <tr>
                <td><strong>{{ $label }}</strong></td>
                <td class="text-center">{{ $prev }}</td>
                <td class="text-center">{{ $now }}</td>
                <td class="text-center {{ $dClass }}"><strong>{{ $dSign }}</strong></td>
            </tr>
        @endforeach
        <tr class="table-secondary">
            <td><strong>Total</strong></td>
            <td class="text-center"><strong>{{ array_sum($previousTotals) }}</strong></td>
            <td class="text-center"><strong>{{ array_sum($currentTotals) }}</strong></td>
            @php
                $totalDelta = array_sum($currentTotals) - array_sum($previousTotals);
                $dClass = $totalDelta < 0 ? 'text-success' : ($totalDelta > 0 ? 'text-danger' : 'text-muted');
                $dSign  = $totalDelta > 0 ? '+' . $totalDelta : ($totalDelta === 0 ? '0' : (string) $totalDelta);
            @endphp
            <td class="text-center {{ $dClass }}"><strong>{{ $dSign }}</strong></td>
        </tr>
    </tbody>
</table>

<div class="d-flex gap-2">
    <a href="{{ route('audits.show', $current) }}" class="btn btn-outline-dark">Voltar a #{{ $current->id }}</a>
    <a href="{{ route('audits.show', $previous) }}" class="btn btn-outline-dark">Abrir #{{ $previous->id }}</a>
</div>

@endsection
