@extends('layouts.app')
@section('title', 'Nova auditoria')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h2 class="mb-3">Iniciar nova auditoria</h2>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('audits.store') }}" id="auditForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">URL alvo</label>
                        <input type="url" name="target_url" value="{{ old('target_url', 'http://localhost:3000') }}"
                               required placeholder="http://localhost:3000"
                               class="form-control @error('target_url') is-invalid @enderror">
                        @error('target_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">
                            Use http://localhost:3000 para auditar o OWASP Juice Shop em Docker.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Destinatarios do relatorio (opcional)</label>
                        <textarea name="recipients" rows="2" class="form-control @error('recipients') is-invalid @enderror"
                                  placeholder="gerente@empresa.mz, manutencao@empresa.mz">{{ old('recipients') }}</textarea>
                        @error('recipients')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">
                            Separados por virgula, ponto-e-virgula ou espaco. Receberao o relatorio PDF por e-mail no fim da auditoria.
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="authorised" value="1" id="authorised" required
                               class="form-check-input @error('authorised') is-invalid @enderror">
                        <label for="authorised" class="form-check-label">
                            Confirmo que tenho autorizacao expressa para auditar este alvo.
                        </label>
                    </div>

                    <button class="btn" style="background:#C8102E; color:#fff;">
                        Iniciar auditoria
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-link">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/audit.js') }}"></script>
@endpush
@endsection
