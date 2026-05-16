@extends('layouts.app')

@section('shellClass', 'light-shell')

{{-- ── Header ───────────────────────────────────────── --}}
@section('header')
    <x-app-header :back="route('home')" title="Emergencia" titleClass="lh-title-danger" />
@endsection

{{-- ── Content ──────────────────────────────────────── --}}
@section('content')

<div class="doc-bg danger"></div>
<div class="doc-wrap d-flex flex-column" style="min-height:100%;">

    {{-- Progress --}}
    <div class="mb-4">
        <x-progress :step="1" :total="3" variant="danger" />
    </div>

    {{-- Hero --}}
    <div class="text-center mb-4">

        <div class="hero-icon mx-auto mb-3">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="13" r="9" fill="#FFFFFF"/>
                <path d="M12 8v5l3.5 2" stroke="#E63946" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5 4.5L2.5 7M19 4.5L21.5 7" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </div>

        <h1 class="lh-question">¿Qué describe mejor<br>tu situación?</h1>
        <p class="lh-subtitle">Selecciona la opción que mejor describa tu situación para poder orientarte de inmediato.</p>
    </div>

    {{-- Option cards --}}
    <div class="d-flex flex-column gap-3">

        <x-option-card
            title="Me notificaron algo urgente"
            subtitle="Recibí un documento oficial con plazo corto de tiempo"
            iconTone="red"
            :attrs="['data-modo' => 'urgente', 'data-card' => 'true']"
        >
            <x-slot:icon>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" width="22" height="22">
                    <path d="M12 2L2 20h20L12 2z" fill="#E63946" opacity=".18"/>
                    <path d="M12 2L2 20h20L12 2z" stroke="#E63946" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M12 9v5" stroke="#E63946" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="16.5" r="1" fill="#E63946"/>
                </svg>
            </x-slot:icon>
        </x-option-card>

        <x-option-card
            title="Me violentaron"
            subtitle="Necesito asesoría de cómo responder"
            iconTone="orange"
            :attrs="['data-modo' => 'violentado', 'data-card' => 'true']"
        >
            <x-slot:icon>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" width="22" height="22">
                    <circle cx="12" cy="12" r="9" fill="#FF6B2B" opacity=".18"/>
                    <circle cx="12" cy="12" r="9" stroke="#FF6B2B" stroke-width="1.8"/>
                    <path d="M8.5 9.5C8.5 9.5 9 8 12 8c3 0 3.5 2 2.5 3.5C13 13 12 13.5 12 15" stroke="#FF6B2B" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="12" cy="17.5" r="1" fill="#FF6B2B"/>
                </svg>
            </x-slot:icon>
        </x-option-card>

        <x-option-card
            title="No entiendo qué hacer"
            subtitle="Me llegó un documento oficial y no sé qué hacer"
            iconTone="purple"
            :attrs="['data-modo' => 'no-se', 'data-card' => 'true']"
        >
            <x-slot:icon>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" width="22" height="22">
                    <circle cx="12" cy="12" r="9" fill="#7C5CBF" opacity=".18"/>
                    <circle cx="12" cy="12" r="9" stroke="#7C5CBF" stroke-width="1.8"/>
                    <path d="M9 9.5C9 8 10.5 7 12 7c1.7 0 3 1.1 3 2.5 0 1.8-2 2.5-2 4" stroke="#7C5CBF" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="12" cy="16.5" r="1" fill="#7C5CBF"/>
                </svg>
            </x-slot:icon>
        </x-option-card>

    </div>

    <div class="flex-grow-1"></div>

    <div class="text-center py-3">
        <a href="#" class="lh-help">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <circle cx="9" cy="9" r="8" stroke="currentColor" stroke-width="1.5"/>
                <path d="M9 13v-1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M6.5 7a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            ¿Cómo decide OrientaVox?
        </a>
    </div>

</div>

@push('styles')
<style>
.hero-icon {
    width: 76px;
    height: 76px;
    background: #E63946;
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 28px rgba(230, 57, 70, 0.35);
}

.hero-icon svg { width: 40px; height: 40px; }

/* Progress en variante danger: el step-label rojo se ve mejor */
.doc-wrap .lh-step-label { color: #94A3B8; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const cards = document.querySelectorAll('[data-card="true"]');
    const setRoute = '/emergencia/set';
    const next = '/consulta/documento';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    cards.forEach(card => {
        card.addEventListener('click', async (e) => {
            e.preventDefault();
            const tipo = card.dataset.modo;
            const titulo = card.querySelector('.lh-card-title')?.textContent?.trim() ?? '';

            sessionStorage.setItem('modo_seleccionado', titulo);

            try {
                await fetch(setRoute, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ tipo }),
                });
            } catch (err) {
                // No bloqueamos la navegación si la sesión falla; el modo queda en sessionStorage
                console.warn('No se pudo guardar el modo en backend:', err);
            }
            window.location.href = next;
        });
    });
})();
</script>
@endpush

@endsection
