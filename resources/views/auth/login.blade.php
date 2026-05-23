@extends('layouts.app')
@section('title', 'Iniciar sessao')
@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo.png') }}" alt="OWASP-AUDIT-MZ" style="max-width: 280px;">
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-3">Iniciar sessao</h5>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Correio electronico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Palavra-passe</label>
                        <input type="password" name="password" required class="form-control">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Manter sessao iniciada</label>
                    </div>
                    <button class="btn w-100" style="background:#C8102E; color:#fff;">Entrar</button>
                </form>
                <hr>
                <p class="mb-0 text-center">
                    <a href="{{ route('register') }}">Criar conta</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
