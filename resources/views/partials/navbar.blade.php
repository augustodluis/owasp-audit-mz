<nav class="navbar navbar-expand-lg shadow-sm" style="background:#fff; border-bottom: 3px solid #C8102E;">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('img/icon-256.png') }}" alt="" height="32" class="me-2">
            <strong>OWASP-AUDIT-MZ</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Painel</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('audits.create') }}">Nova auditoria</a></li>
                @if (auth()->user()->isAdmin())
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.errors') }}">Erros</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">Utilizadores</a></li>
                @endif
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="navbar-text me-3">{{ auth()->user()->name }}</span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-dark">Terminar sessao</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
