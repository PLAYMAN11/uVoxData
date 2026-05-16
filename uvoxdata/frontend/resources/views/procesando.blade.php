@extends('layouts.app')

@section('shellClass', 'light-shell')

@section('header')
    <x-app-header :back="route('home')" title="Procesando" />
@endsection

@section('content')

<div class="procesando-screen">

    {{-- Anillo animado con lupa central --}}
    <div class="ring-wrap">
        <svg class="ring-svg" viewBox="0 0 140 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle class="ring-track" cx="70" cy="70" r="62"/>
            <circle class="ring-dash-small" cx="70" cy="70" r="62"/>
            <circle class="ring-arc" cx="70" cy="70" r="62" transform="rotate(-90 70 70)"/>
        </svg>

        <div class="ring-inner">
            {{-- Lupin 6×6: canvas + /assets/lupin-processing.png; si falla el PNG queda el SVG --}}
            <div id="lupa-procesando" class="lupa-stage lupa-stage--processing">
                <svg viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="18" cy="18" r="12" fill="#E8EEFF" stroke="#4A7CF7" stroke-width="2"/>
                    <ellipse cx="18" cy="18" rx="6" ry="4" fill="#fff"/>
                    <circle cx="18" cy="18" r="2.5" fill="#2A50C8"/>
                    <circle cx="19.2" cy="16.8" r="0.9" fill="#fff"/>
                    <line x1="27" y1="27" x2="34" y2="34" stroke="#6A3FC8" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="procesando-card">
        <h1 class="procesando-title">Respira. Estoy contigo<br>Lo resolveremos paso a paso</h1>
        <p class="procesando-sub">Esto puede tomar unos segundos…</p>

        <div class="procesando-progress">
            <div class="procesando-progress-track">
                <div class="procesando-progress-fill" id="progressFill"></div>
            </div>
        </div>

        <div class="procesando-steps">
            @foreach ([
                'Revisando documento',
                'Identificando tipo de procedimiento',
                'Consultando fuentes oficiales',
                'Generando orientación',
            ] as $idx => $label)
                <div class="procesando-step" id="step{{ $idx + 1 }}">
                    <div class="procesando-step-icon">
                        <svg class="spin-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="8" fill="none" stroke="#DDE0EE" stroke-width="2.5"/>
                            <path d="M10 2a8 8 0 0 1 8 8" fill="none" stroke="#4A7CF7" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                        <svg class="check-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 10l4 4 8-8" stroke="#22C55E" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        </svg>
                    </div>
                    <span class="procesando-step-label">{{ $label }}</span>
                </div>
            @endforeach
        </div>

        <div class="procesando-footer">
            <svg viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <rect x="2" y="6" width="11" height="8" rx="2" stroke="#9298B0" stroke-width="1.5"/>
                <path d="M5 6V4a2.5 2.5 0 0 1 5 0v2" stroke="#9298B0" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="7.5" cy="10" r="1" fill="#9298B0"/>
            </svg>
            <span>No guardamos ningún dato sensible</span>
        </div>
    </div>

</div>

@push('styles')
<style>
.light-shell .app-main { padding: 0; background: linear-gradient(160deg, #F0F2FA 0%, #E8ECF5 40%, #EDF0F8 100%); }

.procesando-screen {
    width: 100%;
    min-height: 100%;
    padding: 32px 0 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.procesando-screen::before,
.procesando-screen::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.procesando-screen::before {
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    border: 28px solid rgba(180,188,220,0.18);
}

.procesando-screen::after {
    top: 30px; right: -30px;
    width: 140px; height: 140px;
    border: 18px solid rgba(180,188,220,0.12);
}

/* Anillo */
.ring-wrap {
    position: relative;
    width: 140px;
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.ring-svg {
    position: absolute;
    inset: 0;
    width: 140px;
    height: 140px;
    animation: ring-rotate 2.4s linear infinite;
    transform-origin: center;
}

@keyframes ring-rotate { to { transform: rotate(360deg); } }

.ring-track {
    fill: none;
    stroke: #D8DCEE;
    stroke-width: 8;
}

.ring-arc {
    fill: none;
    stroke: #4A7CF7;
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 340;
    stroke-dashoffset: 90;
    animation: ring-dash 2.4s ease-in-out infinite;
}

@keyframes ring-dash {
    0%   { stroke-dashoffset: 90; }
    50%  { stroke-dashoffset: 280; }
    100% { stroke-dashoffset: 90; }
}

.ring-dash-small {
    fill: none;
    stroke: #BCC4E8;
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 24 316;
    stroke-dashoffset: -260;
}

.ring-inner {
    width: clamp(104px, 30vmin, 132px);
    height: clamp(104px, 30vmin, 132px);
    background: transparent;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: none;
    z-index: 1;
    flex-shrink: 0;
}

/* Card */
.procesando-card {
    background: rgba(255,255,255,0.86);
    backdrop-filter: blur(12px);
    border-radius: 28px 28px 0 0;
    margin-top: 28px;
    width: 100%;
    max-width: min(100%, 480px);
    margin-left: auto;
    margin-right: auto;
    flex: 1;
    padding: 28px clamp(18px, 5vw, 24px) 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 1;
    box-sizing: border-box;
}

.procesando-title {
    font-size: 20px;
    font-weight: 700;
    color: #1A1D2E;
    text-align: center;
    line-height: 1.35;
    letter-spacing: -0.3px;
    margin: 0 0 8px;
}

.procesando-sub {
    font-size: 14px;
    color: #8A90A8;
    text-align: center;
    margin: 0 0 22px;
}

.procesando-progress {
    width: 100%;
    margin-bottom: 22px;
}

.procesando-progress-track {
    width: 100%;
    height: 8px;
    background: #DDE0EE;
    border-radius: 8px;
    overflow: hidden;
}

.procesando-progress-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #4A7CF7, #6A9BFF);
    border-radius: 8px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.procesando-steps {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.procesando-step {
    display: flex;
    align-items: center;
    gap: 12px;
    opacity: 0.3;
    transition: opacity 0.4s ease;
}

.procesando-step.active,
.procesando-step.done { opacity: 1; }

.procesando-step-icon {
    width: 22px;
    height: 22px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.spin-svg,
.check-svg {
    width: 20px;
    height: 20px;
    display: none;
}

.spin-svg { animation: ov-spin 1s linear infinite; }

.procesando-step.active .spin-svg  { display: block; }
.procesando-step.done   .check-svg { display: block; }

.procesando-step-label {
    font-size: 15px;
    font-weight: 500;
    color: #1A1D2E;
}

.procesando-footer {
    width: 100%;
    margin-top: auto;
    padding-top: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #9298B0;
    font-size: 13px;
}

.procesando-footer svg { width: 15px; height: 15px; opacity: 0.7; }
</style>
@endpush

@push('scripts')
<script>
(function bootProcesando() {
    // Espera a que app.js cargue (define window.OrientaVox.mountLupa)
    function start() {
        // Lupin 6×6 — /assets/lupin-processing.png (fallback al SVG si falta el PNG)
        window.OrientaVox?.mountLupa?.(document.getElementById('lupa-procesando'), { variant: 'processing' });
    }
    if (window.OrientaVox?.mountLupa) start();
    else window.addEventListener('load', start, { once: true });
})();

// ─── Pasos progresivos ─────────────────────────────────────────────────
const STEPS = [
    { id: 'step1', activateAt: 1,  doneAt: 25  },
    { id: 'step2', activateAt: 25, doneAt: 50  },
    { id: 'step3', activateAt: 50, doneAt: 75  },
    { id: 'step4', activateAt: 75, doneAt: 100 },
];

const fill = document.getElementById('progressFill');

function applyProgress(pct) {
    fill.style.width = pct + '%';
    STEPS.forEach(s => {
        const el = document.getElementById(s.id);
        el.classList.remove('active', 'done');
        if      (pct >= s.doneAt)    el.classList.add('done');
        else if (pct >= s.activateAt) el.classList.add('active');
    });
}

// Simulación local del avance — la respuesta real ya está en sessionStorage
// desde el paso anterior (documento o descripción). Damos ~3.2s de "calma"
// y luego redirigimos a resultado.
const REDIRECT_TARGET = '{{ route('consulta.resultado') }}';
const HOME = '{{ route('home') }}';

const hasResult = !!sessionStorage.getItem('rag_resultado');

let pct = 0;
const tick = setInterval(() => {
    pct += 2;
    applyProgress(Math.min(pct, 100));
    if (pct >= 100) {
        clearInterval(tick);
        setTimeout(() => {
            window.location.href = hasResult ? REDIRECT_TARGET : HOME;
        }, 500);
    }
}, 64);
</script>
@endpush


@endsection
