@extends('layouts.app')

@section('shellClass', 'light-shell shell-soft')

@section('header')
    <x-app-header :back="route('consulta.documento')" title="Datos analizados" />
@endsection

@section('content')

{{-- Loading state --}}
<div id="res-loading" class="res-loading">
    <div class="res-spinner"></div>
    <span>Cargando resultado…</span>
</div>

{{-- Error state --}}
<div id="res-error" class="res-loading" hidden>
    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" aria-hidden="true">
        <circle cx="20" cy="20" r="18" stroke="#EF4444" stroke-width="2"/>
        <path d="M20 12v10M20 28v1" stroke="#EF4444" stroke-width="2.5" stroke-linecap="round"/>
    </svg>
    <span id="res-error-msg" style="color:#EF4444; text-align:center;"></span>
    <a href="{{ route('consulta.documento') }}" style="color:#2563EB; font-size:13px;">Volver a intentar</a>
</div>

{{-- Result content --}}
<div id="res-content" class="ov-fade-in" hidden>

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
        <p class="res-urgency-sub" id="res-deadline-sub">—</p>
        <div class="res-urgency-bar-track">
            <div class="res-urgency-bar-fill" id="res-bar" style="width:0%"></div>
        </div>
    </div>

    {{-- Secciones colapsables --}}
    <x-collapsible title="¿Por qué recibiste esto?" tone="purple" :open="true">
        <x-slot:icon>
            <svg width="20" height="20" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="9" stroke="#9333EA" stroke-width="1.7"/>
                <path d="M8.5 8.5a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3" stroke="#9333EA" stroke-width="1.6" stroke-linecap="round"/>
                <circle cx="11" cy="15" r="1" fill="#9333EA"/>
            </svg>
        </x-slot:icon>
        <p class="res-section-body" id="res-porque">—</p>
    </x-collapsible>

    <x-collapsible title="¿Qué puede pasar si no actúas?" tone="yellow" :open="true">
        <x-slot:icon>
            <svg width="20" height="20" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                <path d="M11 3L2 19h18L11 3z" stroke="#D97706" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M11 9v5" stroke="#D97706" stroke-width="1.7" stroke-linecap="round"/>
                <circle cx="11" cy="16" r="1" fill="#D97706"/>
            </svg>
        </x-slot:icon>
        <p class="res-section-body" id="res-consecuencias">—</p>
    </x-collapsible>

    <x-collapsible title="¿Qué puedes hacer ahora?" tone="green" :open="true">
        <x-slot:icon>
            <svg width="20" height="20" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="9" stroke="#16A34A" stroke-width="1.7"/>
                <path d="M6.5 11l3 3 6-6" stroke="#16A34A" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </x-slot:icon>
        <ol class="res-section-list" id="res-acciones"></ol>
    </x-collapsible>

    {{-- Barra inferior de acciones --}}
    <div class="res-action-bar" role="toolbar" aria-label="Acciones rápidas">
        <button type="button" class="res-action" id="action-links" aria-haspopup="dialog" aria-controls="res-links-dialog">
            <span class="res-action-icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M10 13a5 5 0 0 1 7.07 0l1.41 1.41M14 11a5 5 0 0 1-7.07 0L5.52 9.59" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    <path d="M8 12h.01M16 12h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="res-action-text">Links</span>
        </button>

        <button type="button" class="res-action" id="action-escuchar">
            <span class="res-action-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M11 5L6 9H3v6h3l5 4V5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M15 9a3 3 0 0 1 0 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    <path d="M18 6a7 7 0 0 1 0 12" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <span id="action-escuchar-label" class="res-action-text">Escuchar</span>
        </button>

        <a href="{{ route('consulta.documento') }}" class="res-action">
            <span class="res-action-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M14 2v6h6" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M12 18v-6M9 15l3-3 3 3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="res-action-text">Subir un nuevo documento</span>
        </a>
    </div>

    {{-- CTA preguntas --}}
    <div class="res-cta">
        <div class="res-cta-icon">
            <div id="lupa-cta" class="lupa-stage lupa-stage--cta">
                <svg viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="13" cy="13" r="9" stroke="#2563EB" stroke-width="2.2"/>
                    <path d="M20 20l6 6" stroke="#2563EB" stroke-width="2.2" stroke-linecap="round"/>
                    <circle cx="13" cy="13" r="4" fill="#EEF3FD" stroke="#2563EB" stroke-width="1.5"/>
                </svg>
            </div>
        </div>
        <p class="res-cta-title">¿Quieres entender mejor alguna parte?</p>
        <p class="res-cta-sub">Puede sentirse abrumador, pero puedes revisar cualquier sección antes de decidir.</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <button type="button" class="res-btn-si" onclick="window.location.href='{{ route('consulta.chat') }}'">
                Sí, tengo dudas
            </button>
            <button type="button" class="res-btn-no" onclick="window.location.href='{{ route('home') }}'">
                No, estoy bien
            </button>
        </div>
    </div>

</div>

{{-- Modal de enlaces oficiales (fuera de #res-content para que <dialog> y showModal funcionen bien) --}}
<dialog id="res-links-dialog" class="res-links-dlg" aria-labelledby="res-links-dlg-title">
    <div class="res-links-dlg-inner">
        <h3 class="res-links-dlg-title" id="res-links-dlg-title">Enlaces oficiales</h3>
        <p class="res-links-dlg-sub">Abre solo sitios del gobierno o instituciones reconocidas. Comprueba que la dirección sea correcta antes de ingresar datos personales.</p>
        <ul class="res-links-dlg-list">
            <li>
                <a href="https://www.ine.mx/" target="_blank" rel="noopener noreferrer">INE — Instituto Nacional Electoral</a>
            </li>
            <li>
                <a href="https://www.gob.mx/" target="_blank" rel="noopener noreferrer">Gobierno de México (gob.mx)</a>
            </li>
            <li>
                <a href="https://www.tepjf.gob.mx/" target="_blank" rel="noopener noreferrer">TEPJF — Tribunal Electoral del Poder Judicial de la Federación</a>
            </li>
        </ul>
    </div>
    <form method="dialog" class="res-links-dlg-actions">
        <button type="submit">Cerrar</button>
    </form>
</dialog>

@push('scripts')
<script>
(function () {
    /* ────── Lectura del resultado (sessionStorage) ────── */
    const raw = sessionStorage.getItem('rag_resultado');
    if (!raw) return mostrarError('No se encontró ningún resultado. Vuelve a iniciar la consulta.');

    let data;
    try { data = JSON.parse(raw); }
    catch (e) { return mostrarError('Respuesta inválida del servidor.'); }

    const r = data.resultado || {};

    /* ────── Header (tipo + autoridad + badge) ────── */
    document.getElementById('res-tipo').textContent = r.documento_tipo || 'Documento';
    document.getElementById('res-auth').textContent = r.autoridad || '';

    const urgencia = r.urgencia || 'sin_plazo';
    const badgeMap = {
        alta:      { cls: 'res-badge-alta',  txt: '● Actúa Pronto'      },
        media:     { cls: 'res-badge-media', txt: '● Actúa Esta Semana' },
        baja:      { cls: 'res-badge-baja',  txt: '● Con Tiempo'        },
        sin_plazo: { cls: 'res-badge-sin',   txt: '● Sin Plazo'         },
    };
    const bInfo = badgeMap[urgencia] || badgeMap.sin_plazo;
    const badge = document.getElementById('res-badge');
    badge.classList.add(bInfo.cls);
    badge.textContent = bInfo.txt;

    /* ────── Tarjeta de urgencia ────── */
    const card = document.getElementById('res-urgency-card');
    if (urgencia === 'media') card.classList.add('media');
    else if (urgencia === 'baja') card.classList.add('baja');
    else if (urgencia === 'sin_plazo') card.classList.add('sin');

    const deadlineTitle = document.getElementById('res-deadline-title');
    const sub = document.getElementById('res-deadline-sub');
    const bar = document.getElementById('res-bar');

    if (r.fecha_limite_iso) {
        const hoy = new Date(); hoy.setHours(0,0,0,0);
        const limit = new Date(r.fecha_limite_iso + 'T00:00:00');
        const dias = Math.ceil((limit - hoy) / 86400000);

        deadlineTitle.textContent = r.fecha_limite_texto
            ? `Actúa antes del ${r.fecha_limite_texto}`
            : 'Actúa antes del plazo';

        if      (dias < 0)  sub.textContent = 'El plazo ha vencido';
        else if (dias === 0) sub.textContent = 'Tienes menos de 1 día';
        else if (dias === 1) sub.textContent = 'Tienes 1 día';
        else                 sub.textContent = `Tienes ${dias} días`;

        const pct = urgencia === 'alta' ? 88 : urgencia === 'media' ? 58 : 28;
        bar.style.width = pct + '%';
    } else if (urgencia === 'sin_plazo') {
        deadlineTitle.textContent = r.documento_tipo || 'Documento';
        sub.textContent = 'Sin fecha límite identificada';
        bar.style.width = '18%';
    } else {
        deadlineTitle.textContent = r.fecha_limite_texto ? `Actúa antes del ${r.fecha_limite_texto}` : 'Actúa pronto';
        sub.textContent = '';
        bar.style.width = urgencia === 'alta' ? '88%' : '55%';
    }

    /* ────── Secciones colapsables ────── */
    document.getElementById('res-porque').textContent = r.por_que_lo_recibiste || '—';
    document.getElementById('res-consecuencias').textContent = r.consecuencias || '—';

    const ol = document.getElementById('res-acciones');
    const acciones = Array.isArray(r.acciones) && r.acciones.length
        ? r.acciones
        : ['Consulta directamente al TEE Chihuahua'];
    acciones.forEach(a => {
        const li = document.createElement('li');
        li.textContent = a;
        ol.appendChild(li);
    });

    /* ────── Mostrar contenido ────── */
    document.getElementById('res-loading').hidden = true;
    document.getElementById('res-content').hidden = false;

    /* ────── Collapsibles ────── */
    document.querySelectorAll('[data-collapsible]').forEach(sec => {
        const head = sec.querySelector('.res-section-head');
        head.addEventListener('click', () => {
            const open = sec.dataset.open === 'true';
            sec.dataset.open = open ? 'false' : 'true';
            head.setAttribute('aria-expanded', String(!open));
        });
    });

    /* ────── Botón Escuchar (Web Speech API) ────── */
    const btnEscuchar = document.getElementById('action-escuchar');
    const lblEscuchar = document.getElementById('action-escuchar-label');
    let utter = null;

    btnEscuchar.addEventListener('click', () => {
        if (!('speechSynthesis' in window)) {
            alert('Tu navegador no soporta lectura por voz.');
            return;
        }

        if (window.speechSynthesis.speaking) {
            window.speechSynthesis.cancel();
            btnEscuchar.classList.remove('active');
            lblEscuchar.textContent = 'Escuchar';
            return;
        }

        const texto = [
            r.documento_tipo,
            deadlineTitle.textContent,
            sub.textContent,
            r.por_que_lo_recibiste,
            r.consecuencias,
            'Lo que puedes hacer: ' + acciones.join('. '),
        ].filter(Boolean).join('. ');

        utter = new SpeechSynthesisUtterance(texto);
        utter.lang = 'es-MX';
        utter.rate = 1.0;
        utter.onend = () => { btnEscuchar.classList.remove('active'); lblEscuchar.textContent = 'Escuchar'; };
        utter.onerror = utter.onend;

        window.speechSynthesis.speak(utter);
        btnEscuchar.classList.add('active');
        lblEscuchar.textContent = 'Detener';
    });

    /* ────── Botón Links — modal con enlaces oficiales ────── */
    const btnLinks = document.getElementById('action-links');
    const dlgLinks = document.getElementById('res-links-dialog');
    if (btnLinks && dlgLinks) {
        btnLinks.addEventListener('click', function () {
            if (typeof dlgLinks.showModal === 'function') {
                try { dlgLinks.showModal(); }
                catch (e) { window.open('https://www.gob.mx/', '_blank', 'noopener,noreferrer'); }
            } else {
                window.open('https://www.gob.mx/', '_blank', 'noopener,noreferrer');
            }
        });
    }

    /* Lupin 5×5 — /assets/lupin-default.png (fallback al SVG si falta el PNG) */
    const mountLupa = window.OrientaVox?.mountLupa;
    if (mountLupa) {
        mountLupa(document.getElementById('lupa-cta'), { variant: 'default' });
    } else {
        window.addEventListener('load', () => {
            window.OrientaVox?.mountLupa?.(document.getElementById('lupa-cta'), { variant: 'default' });
        }, { once: true });
    }

    function mostrarError(msg) {
        document.getElementById('res-loading').hidden = true;
        document.getElementById('res-error-msg').textContent = msg;
        document.getElementById('res-error').hidden = false;
    }
})();
</script>
@endpush

@endsection
