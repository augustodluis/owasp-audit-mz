@extends('layouts.app')
@section('title', 'Utilizadores')
@section('content')
<h2 class="mb-3">Utilizadores</h2>
<table class="table align-middle">
    <thead class="table-light"><tr><th>Nome</th><th>Email</th><th>Papel</th><th>Criado</th><th></th></tr></thead>
    <tbody>
    @foreach ($users as $u)
        <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td><span class="badge text-bg-secondary">{{ $u->role }}</span></td>
            <td>{{ $u->created_at?->format('Y-m-d') }}</td>
            <td class="text-end">
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                      onsubmit="return confirm('Eliminar utilizador?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $users->links() }}
@endsection
