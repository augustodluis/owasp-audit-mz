@extends('layouts.app')
@section('title', 'Notificacoes')
@section('content')
<h2 class="mb-3">As suas notificacoes</h2>
<ul class="list-group">
    @forelse ($notifications as $n)
        <li class="list-group-item d-flex justify-content-between align-items-start
                   {{ $n->read_flag ? 'text-muted' : 'fw-semibold' }}">
            <div>
                <span class="badge text-bg-{{ $n->type === 'critical' ? 'danger' : ($n->type === 'warning' ? 'warning' : 'info') }} me-2">
                    {{ $n->type }}
                </span>
                {{ $n->message }}
                <div><small>{{ $n->created_at?->format('Y-m-d H:i') }}</small></div>
            </div>
            @unless ($n->read_flag)
                <form method="POST" action="{{ route('notifications.read', $n) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-secondary">Marcar lida</button>
                </form>
            @endunless
        </li>
    @empty
        <li class="list-group-item">Sem notificacoes.</li>
    @endforelse
</ul>
{{ $notifications->links() }}
@endsection
