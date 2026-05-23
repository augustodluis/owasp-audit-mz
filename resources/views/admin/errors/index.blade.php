@extends('layouts.app')
@section('title', 'Erros internos')
@section('content')
<h2 class="mb-3">Painel interno de erros</h2>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="level" class="form-select">
            <option value="">Todos os niveis</option>
            <option value="error"    @selected(request('level')==='error')>error</option>
            <option value="warning"  @selected(request('level')==='warning')>warning</option>
            <option value="critical" @selected(request('level')==='critical')>critical</option>
        </select>
    </div>
    <div class="col-md-3"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
    <div class="col-md-3"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
    <div class="col-md-3"><button class="btn btn-outline-dark">Filtrar</button></div>
</form>

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>Data</th><th>Nivel</th><th>Mensagem</th><th>Ficheiro</th><th>Linha</th><th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($errors as $err)
                <tr>
                    <td>{{ $err->created_at?->format('Y-m-d H:i:s') }}</td>
                    <td><span class="badge text-bg-danger">{{ $err->level }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($err->message, 110) }}</td>
                    <td><code>{{ basename($err->file) }}</code></td>
                    <td>{{ $err->line }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.errors.resolve', $err) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-success">Resolver</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $errors->links() }}
@endsection
