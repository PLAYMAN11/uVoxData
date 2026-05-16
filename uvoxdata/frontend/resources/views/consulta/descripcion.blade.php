@extends('layouts.app')

@section('shellClass', 'light-shell')

{{-- ── Header ───────────────────────────────────────── --}}
@section('header')
<div class="lh-header w-100 d-flex align-items-center justify-content-between px-3">
    <a href="{{ route('consulta.documento') }}" class="lh-back d-flex align-items-center">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <path d="M13 5l-6 6 6 6" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <span class="lh-title">Situación</span>
    <button class="lh-menu-btn" aria-label="Menú">
        <svg width="18" height="14" viewBox="0 0 18 14" fill="none">
            <path d="M1 1h16M1 7h16M1 13h16" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>
</div>
@endsection

{{-- ── Styles ───────────────────────────────────────── --}}
@push('styles')
<style>
/* Reutiliza light-shell, lh-header, lh-back, lh-title, lh-menu-btn de documento.blade.php */
.light-shell { background: #FFFFFF !important; }
.light-shell .app-header { background: #FFFFFF; border-bottom: 1px solid #E9EEF6; padding-top: 16px; padding-bottom: 16px; height: auto; min-height: 56px; }
.light-shell .app-main   { background: #FFFFFF; padding: 28px 20px 20px; position: relative; }
.light-shell .app-bottom { background: #FFFFFF; border-top: 1px solid #E9EEF6; }

.lh-header   { height: 100%; }
.lh-back     { color: #1E293B; text-decoration: none; }
.lh-title    { font-size: 17px; font-weight: 700; color: #1E293B; letter-spacing: -0.01em; }
.lh-menu-btn {
    background: #FFFFFF; border: 1.5px solid #D1D9E6; border-radius: 10px;
    width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;
    cursor: pointer; padding: 0;
}
.lh-menu-btn:hover { background: #F1F5F9; }

/* Decoration circles */
.doc-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
.doc-bg::before {
    content: ''; position: absolute; width: 280px; height: 280px; border-radius: 50%;
    background: radial-gradient(circle, #C7D9F8 0%, transparent 70%);
    top: -80px; right: -100px; opacity: 0.45;
}
.doc-bg::after {
    content: ''; position: absolute; width: 220px; height: 220px; border-radius: 50%;
    background: radial-gradient(circle, #D4E4FB 0%, transparent 70%);
    bottom: 40px; right: -60px; opacity: 0.35;
}
.doc-wrap { position: relative; z-index: 1; width: 100%; }

/* Step / progress */
.lh-step-label   { font-size: 12px; font-weight: 500; color: #64748B; letter-spacing: 0.01em; }
.lh-progress-row { display: flex; align-items: center; gap: 10px; }
.lh-progress-track { flex: 1; height: 6px; background: #E2E8F0; border-radius: 99px; overflow: hidden; }
.lh-progress-fill  { height: 100%; background: #2F6FE8; border-radius: 99px; }

/* Title */
.nd-title    { font-size: 24px; font-weight: 800; color: #0F1D35; line-height: 1.25; letter-spacing: -0.02em; }
.nd-subtitle { font-size: 14px; color: #64748B; line-height: 1.55; }

/* Textarea */
.nd-textarea {
    width: 100%;
    min-height: 160px;
    border: 1.5px solid #D1DCF0;
    border-radius: 14px;
    padding: 14px 16px;
    font-size: 14px;
    color: #1E293B;
    background: #FAFBFF;
    resize: none;
    outline: none;
    font-family: inherit;
    line-height: 1.6;
    transition: border-color .15s, box-shadow .15s;
}
.nd-textarea::placeholder { color: #94A3B8; }
.nd-textarea:focus {
    border-color: #2F6FE8;
    box-shadow: 0 0 0 3px rgba(47,111,232,.1);
    background: #FFFFFF;
}

/* Enviar button */
.nd-btn-enviar {
    background: #5FA7E8;
    color: #FFFFFF;
    border: none;
    border-radius: 14px;
    padding: 12px 28px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s, opacity .15s;
}
.nd-btn-enviar:hover   { background: #2F6FE8; }
.nd-btn-enviar:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
@endpush

{{-- ── Content ─────────────────────────────────────── --}}
@section('content')

<div class="doc-bg"></div>
<div class="doc-wrap d-flex flex-column" style="min-height:100%;">

    {{-- Paso 3 de 3 --}}
    <div class="mb-4">
        <p class="lh-step-label mb-2">Paso 3 de 3</p>
        <div class="lh-progress-row">
            <div class="lh-progress-track">
                <div class="lh-progress-fill" style="width:100%"></div>
            </div>
            {{-- Check verde --}}
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" flex-shrink="0">
                <circle cx="11" cy="11" r="10" stroke="#22C55E" stroke-width="1.8"/>
                <path d="M6.5 11l3 3 6-6" stroke="#22C55E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>

    {{-- Title --}}
    <div class="mb-4">
        <h1 class="nd-title mb-2">Describe tu situación</h1>
        <p class="nd-subtitle mb-0">No importa si no sabes los términos legales.<br>Escribe lo que recuerdas del documento.</p>
    </div>

    {{-- Form --}}
    <form id="form-describir">
        @csrf
        <textarea
            id="txt-descripcion"
            name="pregunta"
            class="nd-textarea mb-3"
            placeholder="Escribe tu situación y lo que recuerdes del documento"
            maxlength="3000"
        ></textarea>

        <button type="submit" class="nd-btn-enviar" id="btn-enviar">Enviar</button>
    </form>

    <div class="flex-grow-1"></div>

</div>

@endsection

@push('scripts')
<script>
document.getElementById('form-describir').addEventListener('submit', function (e) {
    e.preventDefault();

    const texto = document.getElementById('txt-descripcion').value.trim();
    if (!texto) {
        document.getElementById('txt-descripcion').focus();
        return;
    }

    const btn = document.getElementById('btn-enviar');
    btn.disabled = true;
    btn.textContent = 'Enviando…';

    fetch('{{ route("consulta.describir") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ pregunta: texto }),
    })
    .then(res => {
        if (!res.ok) return res.json().then(e => Promise.reject(e));
        return res.json();
    })
    .then(data => {
        sessionStorage.setItem('rag_resultado', JSON.stringify(data));
        window.location.href = '{{ route("consulta.resultado") }}';
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Enviar';
        alert('Error al enviar. Intenta de nuevo.');
        console.error(err);
    });
});
</script>
@endpush
