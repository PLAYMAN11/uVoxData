@extends('layouts.app')

@section('shellClass', 'light-shell')

{{-- ── Header ──────────────────────────────────────── --}}
@section('header')
    <x-app-header :back="route('consulta.documento')" title="Situación" />
@endsection

{{-- ── Content ─────────────────────────────────────── --}}
@section('content')

<div class="doc-bg"></div>
<div class="doc-wrap d-flex flex-column" style="min-height:100%;">

    {{-- Progress --}}
    <div class="mb-4">
        <x-progress :step="3" :total="3" :showCheck="true" />
    </div>

    {{-- Title --}}
    <div class="mb-4">
        <h1 class="lh-question">Describe tu situación</h1>
        <p class="lh-subtitle">No importa si no sabes los términos legales.<br>Escribe lo que recuerdas del documento.</p>
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
            rows="6"
        ></textarea>

        <button type="submit" class="nd-btn-enviar" id="btn-enviar">Enviar</button>
    </form>

    <div class="flex-grow-1"></div>

</div>

@push('scripts')
<script>
document.getElementById('form-describir').addEventListener('submit', async function (e) {
    e.preventDefault();

    const txt = document.getElementById('txt-descripcion');
    const texto = txt.value.trim();

    if (!texto) {
        txt.focus();
        return;
    }

    const btn = document.getElementById('btn-enviar');
    btn.disabled = true;
    btn.textContent = 'Enviando…';

    sessionStorage.removeItem('rag_resultado');

    try {
        let data;

        const usarOffline = async () => {
            const ov = window.OrientaVox;
            if (!ov?.orientacionDesdeTexto) {
                throw new Error('Módulo de orientación aún no cargado. Espera un segundo e inténtalo de nuevo.');
            }
            return ov.orientacionDesdeTexto(texto);
        };

        if (!navigator.onLine) {
            data = await usarOffline();
        } else {
            const res = await fetch('/consulta/describir', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ pregunta: texto }),
            });
            if (!res.ok) {
                if (res.status === 503) {
                    throw new Error(
                        'El análisis en línea no está disponible (el backend no responde). Comprueba BACKEND_URL o Docker.'
                    );
                }
                throw new Error('Error al enviar.');
            }
            data = await res.json();
        }

        sessionStorage.setItem('rag_resultado', JSON.stringify(data));
        window.location.href = '{{ route("consulta.procesando") }}';
    } catch (err) {
        if (!navigator.onLine) {
            try {
                const data = await window.OrientaVox?.orientacionDesdeTexto?.(texto);
                if (data) {
                    sessionStorage.setItem('rag_resultado', JSON.stringify(data));
                    window.location.href = '{{ route("consulta.procesando") }}';
                    return;
                }
            } catch (_) { /* seguir */ }
        }

        btn.disabled = false;
        btn.textContent = 'Enviar';
        alert(err?.message || 'No se pudo enviar la descripción. Comprueba conexión y que el backend esté en marcha (BACKEND_URL).');
        console.error(err);
    }
});
</script>
@endpush

@endsection
