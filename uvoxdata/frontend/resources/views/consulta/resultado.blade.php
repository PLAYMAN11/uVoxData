@extends('layouts.app')

@section('shellClass', 'light-shell shell-soft')

{{-- ── Header ──────────────────────────────────────── --}}
@section('header')
    <x-app-header :back="route('consulta.documento')" title="Resultado" />
@endsection

{{-- ── Content ─────────────────────────────────────── --}}
@section('content')

{{-- Loading state --}}
<div id="res-loading" class="res-loading">
    <div class="res-spinner"></div>
    <span>Cargando resultado…</span>
</div>

{{-- Error state --}}
<div id="res-error" style="display:none;" class="res-loading">
    <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
        <circle cx="20" cy="20" r="18" stroke="#EF4444" stroke-width="2"/>
        <path d="M20 12v10M20 28v1" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round"/>
    </svg>
    <span id="res-error-msg" style="color:#EF4444; text-align:center;"></span>
    <a href="{{ route('consulta.documento') }}" style="color:#2563EB; font-size:13px;">Volver a intentar</a>
</div>

{{-- Result content --}}
<div id="res-content" style="display:none;">

    {{-- Doc header row --}}
    <div class="res-doc-row">
        <div class="d-flex align-items-center gap-3">
            <div class="res-doc-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M14 2v6h6" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M8 13h8M8 17h5" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <p class="res-doc-tipo" id="res-tipo">—</p>
                <p class="res-doc-auth" id="res-auth">—</p>
            </div>
        </div>
        <span class="res-badge" id="res-badge">—</span>
    </div>

    {{-- Urgency card --}}
    <div class="res-urgency" id="res-urgency-card">
        <p class="res-urgency-title" id="res-deadline-title">—</p>
        <p class="res-urgency-sub"   id="res-deadline-sub">—</p>
        <div class="res-urgency-bar-track">
            <div class="res-urgency-bar-fill" id="res-bar" style="width:0%"></div>
        </div>
    </div>

    {{-- ¿Por qué lo recibiste? --}}
    <div class="res-section" data-open="true">
        <button class="res-section-head" type="button" aria-expanded="true">
            <div class="res-section-icon purple">
                <svg width="20" height="20" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="9" stroke="#9333EA" stroke-width="1.7"/>
                    <path d="M8.5 8.5a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3" stroke="#9333EA" stroke-width="1.6" stroke-linecap="round"/>
                    <circle cx="11" cy="15" r="1" fill="#9333EA"/>
                </svg>
            </div>
            <p class="res-section-title">¿Por qué recibiste esto?</p>
            <svg class="res-section-toggle" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <path d="M4.5 6.5l4.5 5 4.5-5" stroke="#94A3B8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <p class="res-section-body" id="res-porque">—</p>
    </div>

    {{-- ¿Qué puede pasar si no actúas? --}}
    <div class="res-section" data-open="true">
        <button class="res-section-head" type="button" aria-expanded="true">
            <div class="res-section-icon yellow">
                <svg width="20" height="20" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                    <path d="M11 3L2 19h18L11 3z" stroke="#D97706" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M11 9v5" stroke="#D97706" stroke-width="1.7" stroke-linecap="round"/>
                    <circle cx="11" cy="16" r="1" fill="#D97706"/>
                </svg>
            </div>
            <p class="res-section-title">¿Qué puede pasar si no actúas?</p>
            <svg class="res-section-toggle" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <path d="M4.5 6.5l4.5 5 4.5-5" stroke="#94A3B8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <p class="res-section-body" id="res-consecuencias">—</p>
    </div>

    {{-- ¿Qué puedes hacer ahora? --}}
    <div class="res-section" data-open="true">
        <button class="res-section-head" type="button" aria-expanded="true">
            <div class="res-section-icon green">
                <svg width="20" height="20" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="9" stroke="#16A34A" stroke-width="1.7"/>
                    <path d="M6.5 11l3 3 6-6" stroke="#16A34A" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="res-section-title">¿Qué puedes hacer ahora?</p>
            <svg class="res-section-toggle" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <path d="M4.5 6.5l4.5 5 4.5-5" stroke="#94A3B8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <ol class="res-section-list" id="res-acciones"></ol>
    </div>

    {{-- CTA ── ¿Quieres entender mejor? --}}
    <div class="res-cta">
        <div class="res-cta-icon">
            <div id="lupi-cta" class="lupa-stage lupa-stage--cta">
                <svg viewBox="0 0 42 42" fill="none" aria-hidden="true">
                    <circle cx="18" cy="18" r="12" fill="#E8EEFF" stroke="#4A7CF7" stroke-width="2"/>
                    <ellipse cx="18" cy="18" rx="6" ry="4" fill="#fff"/>
                    <circle cx="18" cy="18" r="2.5" fill="#2A50C8"/>
                    <circle cx="19.2" cy="16.8" r="0.9" fill="#fff"/>
                    <line x1="27" y1="27" x2="34" y2="34" stroke="#6A3FC8" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        <p class="res-cta-title">¿Quieres entender mejor alguna parte?</p>
        <p class="res-cta-sub">Puede sentirse abrumador, pero puedes revisar cualquier sección antes de decidir.</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <button class="res-btn-si" onclick="window.location.href='{{ route('consulta.descripcion') }}'">
                Sí, tengo dudas
            </button>
            <button class="res-btn-no" onclick="window.location.href='{{ route('home') }}'">
                No, estoy bien
            </button>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    /* ── Collapsible sections ───────────────────────── */
    document.querySelectorAll('.res-section-head').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const section = btn.closest('.res-section');
            const isOpen  = section.dataset.open === 'true';
            section.dataset.open = isOpen ? 'false' : 'true';
            btn.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    /* ── Load RAG result ────────────────────────────── */
    const raw = sessionStorage.getItem('rag_resultado');

    if (!raw) {
        mostrarError('No se encontró ningún resultado. Vuelve a iniciar la consulta.');
        return;
    }

    let data;
    try { data = JSON.parse(raw); } catch (e) {
        mostrarError('Respuesta inválida del servidor.');
        return;
    }

    // El backend devuelve { ok, resultado: { documento_tipo, autoridad, urgencia, acciones, ... } }
    const r = data.resultado || {};

    // ── Doc header ────────────────────────────────────
    const tipoRaw = r.documento_tipo || 'Documento electoral';
    document.getElementById('res-tipo').textContent = tipoRaw.replace(/_/g, ' ');
    document.getElementById('res-auth').textContent = r.autoridad || '';

    // ── Badge según urgencia ──────────────────────────
    const badge    = document.getElementById('res-badge');
    const urgencia = r.urgencia || 'sin_plazo';
    const badgeMap = {
        alta:      { cls: 'res-badge-alta',  txt: '● Actúa Pronto'      },
        media:     { cls: 'res-badge-media', txt: '● Requiere Atención' },
        baja:      { cls: 'res-badge-baja',  txt: '● Informativo'       },
        sin_plazo: { cls: 'res-badge-sin',   txt: '● Sin Plazo'         },
    };
    const bInfo = badgeMap[urgencia] || badgeMap['sin_plazo'];
    badge.classList.add(bInfo.cls);
    badge.textContent = bInfo.txt;

    // ── Urgency card ──────────────────────────────────
    const card = document.getElementById('res-urgency-card');
    if (urgencia === 'alta')           card.classList.add('alta');
    else if (urgencia === 'media')     card.classList.add('media');
    else if (urgencia === 'baja')      card.classList.add('baja');
    else                               card.classList.add('sin');

    const fechaTxt = r.fecha_limite_texto || '';
    document.getElementById('res-deadline-title').textContent =
        tipoRaw.replace(/_/g, ' ');

    const sub = document.getElementById('res-deadline-sub');
    const bar = document.getElementById('res-bar');
    if (fechaTxt && fechaTxt !== 'No se encontró') {
        sub.textContent = fechaTxt;
        bar.style.width = urgencia === 'alta' ? '88%' : urgencia === 'media' ? '55%' : '20%';
    } else {
        sub.textContent = 'Sin fecha límite identificada';
        bar.style.width = '18%';
    }

    // ── Info sections ─────────────────────────────────
    const porQue = r.por_que_lo_recibiste || '—';
    const consec = r.consecuencias        || '—';
    document.getElementById('res-porque').textContent       = porQue !== 'No se encontró' ? porQue : '—';
    document.getElementById('res-consecuencias').textContent = consec !== 'No se encontró' ? consec : '—';

    const ol      = document.getElementById('res-acciones');
    const actions = Array.isArray(r.acciones) ? r.acciones : [];
    if (actions.length) {
        actions.forEach(function (a) {
            const li = document.createElement('li');
            li.textContent = a;
            ol.appendChild(li);
        });
    } else {
        ol.innerHTML = '<li>Consulta directamente al TEE Chihuahua</li>';
    }

    // ── Mostrar contenido ─────────────────────────────
    document.getElementById('res-loading').style.display = 'none';
    document.getElementById('res-content').style.display = 'block';
})();

function mostrarError(msg) {
    document.getElementById('res-loading').style.display = 'none';
    document.getElementById('res-error-msg').textContent = msg;
    document.getElementById('res-error').style.display  = 'flex';
}

/* ── Lupi mascota ────────────────────────────────── */
(function () {
    function mountLupi() {
        window.OrientaVox?.mountLupa?.(
            document.getElementById('lupi-cta'),
            { variant: 'default' }
        );
    }
    if (window.OrientaVox?.mountLupa) mountLupi();
    else window.addEventListener('load', mountLupi, { once: true });
})();
</script>
@endpush
