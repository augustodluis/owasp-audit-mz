@extends('layouts.app')
@section('title', 'Registar conta')
@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo.png') }}" alt="OWASP-AUDIT-MZ" style="max-width: 280px;">
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-3">Registar nova conta</h5>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correio electronico</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Palavra-passe</label>
                        <input type="password" name="password" required
                               class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Minimo 8 caracteres, com letras e digitos.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar palavra-passe</label>
                        <input type="password" name="password_confirmation" required class="form-control">
                    </div>
                    <button class="btn w-100" style="background:#C8102E; color:#fff;">Criar conta</button>
                </form>
                <hr>
                <p class="mb-0 text-center">
                    Ja tem conta? <a href="{{ route('login') }}">Inicie sessao</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
