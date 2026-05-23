@extends('layouts.app')
@section('title', 'Painel')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">As suas auditorias</h2>
    <a href="{{ route('audits.create') }}" class="btn" style="background:#C8102E; color:#fff;">
        Nova auditoria
    </a>
</div>

@if ($audits->isEmpty())
    <div class="alert alert-info">
        Ainda nao iniciou nenhuma auditoria. Clique em "Nova auditoria" para comecar.
    </div>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Alvo</th>
                    <th>Estado</th>
                    <th>Iniciada</th>
                    <th>Concluida</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($audits as $audit)
                    <tr>
                        <td>{{ $audit->id }}</td>
                        <td><code>{{ $audit->target_url }}</code></td>
                        <td>
                            @php $badge = match($audit->status) {
                                'completed' => 'success',
                                'running'   => 'primary',
                                'failed'    => 'danger',
                                'cancelled' => 'secondary',
                                default     => 'warning',
                            }; @endphp
                            <span class="badge text-bg-{{ $badge }}">{{ $audit->status }}</span>
                        </td>
                        <td>{{ $audit->started_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $audit->finished_at?->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('audits.show', $audit) }}" class="btn btn-sm btn-outline-dark">Abrir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $audits->links() }}
@endif
@endsection
