@extends('layouts.app')

@section('shellClass', 'light-shell')

@section('header')
<div class="lh-header w-100 d-flex align-items-center justify-content-between px-3">
    <a href="{{ route('consulta.documento') }}" class="lh-back d-flex align-items-center">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <path d="M13 5l-6 6 6 6" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <span class="lh-title">Resultado</span>
    <button class="lh-menu-btn" aria-label="Menú">
        <svg width="18" height="14" viewBox="0 0 18 14" fill="none">
            <path d="M1 1h16M1 7h16M1 13h16" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>
</div>
@endsection

@push('styles')
<style>
.light-shell { background: #F1F5FB !important; }
.light-shell .app-header { background: #FFFFFF; border-bottom: 1px solid #E9EEF6; padding-top: 16px; padding-bottom: 16px; height: auto; min-height: 56px; }
.light-shell .app-main   { background: #F1F5FB; padding: 20px 16px; position: relative; }
.light-shell .app-bottom { background: #FFFFFF; border-top: 1px solid #E9EEF6; }

.lh-header { height: 100%; }
.lh-back   { color: #1E293B; text-decoration: none; }
.lh-title  { font-size: 17px; font-weight: 700; color: #1E293B; }
.lh-menu-btn { background:#FFFFFF; border:1.5px solid #D1D9E6; border-radius:10px; width:38px; height:38px; display:flex; align-items:center; justify-content:center; cursor:pointer; padding:0; }

/* ── Doc header row ── */
.res-doc-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.res-doc-icon { width:52px; height:52px; background:#EEF3FD; border-radius:13px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.res-doc-tipo { font-size:14px; font-weight:700; color:#1E293B; margin:0; }
.res-doc-auth { font-size:12px; color:#64748B; margin:0; }
.res-badge { font-size:11px; font-weight:700; padding:5px 10px; border-radius:99px; white-space:nowrap; }
.res-badge-alta  { background:#FEE2E2; color:#DC2626; }
.res-badge-media { background:#FEF3C7; color:#D97706; }
.res-badge-baja  { background:#DCFCE7; color:#16A34A; }
.res-badge-sin   { background:#E2E8F0; color:#64748B; }

/* ── Urgency card ── */
.res-urgency {
    border-radius:16px; padding:18px; margin-bottom:14px;
    border: 1.5px solid #FECACA; background:#FFF5F5;
}
.res-urgency.media { border-color:#FDE68A; background:#FFFBEB; }
.res-urgency.baja  { border-color:#BBF7D0; background:#F0FDF4; }
.res-urgency.sin   { border-color:#CBD5E1; background:#F8FAFC; }
.res-urgency-title { font-size:22px; font-weight:800; color:#0F1D35; line-height:1.25; margin:0 0 4px; }
.res-urgency-sub   { font-size:14px; font-weight:600; color:#DC2626; margin:0 0 12px; }
.res-urgency.media .res-urgency-sub { color:#D97706; }
.res-urgency.baja  .res-urgency-sub { color:#16A34A; }
.res-urgency.sin   .res-urgency-sub { color:#64748B; }
.res-urgency-bar-track { height:8px; background:rgba(0,0,0,0.08); border-radius:99px; overflow:hidden; }
.res-urgency-bar-fill  { height:100%; border-radius:99px; background:#DC2626; transition:width .4s; }
.res-urgency.media .res-urgency-bar-fill { background:#F59E0B; }
.res-urgency.baja  .res-urgency-bar-fill { background:#22C55E; }
.res-urgency.sin   .res-urgency-bar-fill { background:#94A3B8; }

/* ── Info cards ── */
.res-card { background:#FFFFFF; border:1.5px solid #E2EAF4; border-radius:16px; padding:16px; margin-bottom:14px; display:flex; align-items:flex-start; gap:14px; }
.res-card-icon { width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; }
.res-card-icon.purple { background:#F3E8FF; }
.res-card-icon.yellow { background:#FEF9C3; }
.res-card-icon.green  { background:#DCFCE7; }
.res-card-title { font-size:14px; font-weight:700; color:#1E293B; margin:0 0 6px; }
.res-card-body  { font-size:13px; color:#475569; line-height:1.55; margin:0; }
.res-card-list  { font-size:13px; color:#475569; line-height:1.7; margin:0; padding-left:18px; }

/* ── CTA section ── */
.res-cta { background:#FFFFFF; border:1.5px solid #E2EAF4; border-radius:16px; padding:24px 16px; text-align:center; }
.res-cta-icon { width:64px; height:64px; background:#EEF3FD; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }
.res-cta-title { font-size:15px; font-weight:700; color:#1E293B; margin:0 0 6px; }
.res-cta-sub   { font-size:13px; color:#64748B; margin:0 0 18px; line-height:1.5; }
.res-btn-si  { background:#5FA7E8; color:#FFF; border:none; border-radius:14px; padding:13px 28px; font-size:14px; font-weight:700; cursor:pointer; }
.res-btn-si:hover { background:#2F6FE8; }
.res-btn-no  { background:#FFF; color:#1E293B; border:1.5px solid #D1D9E6; border-radius:14px; padding:13px 28px; font-size:14px; font-weight:700; cursor:pointer; }
.res-btn-no:hover { background:#F1F5F9; }

/* ── Loading / error ── */
.res-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:300px; gap:12px; color:#64748B; font-size:14px; }
.res-spinner { width:36px; height:36px; border:3px solid #E2E8F0; border-top-color:#2F6FE8; border-radius:50%; animation:spin .7s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>
@endpush

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
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
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
    <div class="res-card">
        <div class="res-card-icon purple">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                <circle cx="11" cy="11" r="9" stroke="#9333EA" stroke-width="1.7"/>
                <path d="M8.5 8.5a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3" stroke="#9333EA" stroke-width="1.6" stroke-linecap="round"/>
                <circle cx="11" cy="15" r="1" fill="#9333EA"/>
            </svg>
        </div>
        <div>
            <p class="res-card-title">¿Por qué recibiste esto?</p>
            <p class="res-card-body" id="res-porque">—</p>
        </div>
    </div>

    {{-- ¿Qué puede pasar si no actúas? --}}
    <div class="res-card">
        <div class="res-card-icon yellow">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                <path d="M11 3L2 19h18L11 3z" stroke="#D97706" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M11 9v5" stroke="#D97706" stroke-width="1.7" stroke-linecap="round"/>
                <circle cx="11" cy="16" r="1" fill="#D97706"/>
            </svg>
        </div>
        <div>
            <p class="res-card-title">¿Qué puede pasar si no actúas?</p>
            <p class="res-card-body" id="res-consecuencias">—</p>
        </div>
    </div>

    {{-- ¿Qué puedes hacer ahora? --}}
    <div class="res-card">
        <div class="res-card-icon green">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                <circle cx="11" cy="11" r="9" stroke="#16A34A" stroke-width="1.7"/>
                <path d="M6.5 11l3 3 6-6" stroke="#16A34A" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div style="flex:1;">
            <p class="res-card-title">¿Qué puedes hacer ahora?</p>
            <ol class="res-card-list" id="res-acciones"></ol>
        </div>
    </div>

    {{-- CTA ── ¿Quieres entender mejor? --}}
    <div class="res-cta">
        <div class="res-cta-icon">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none">
                <circle cx="13" cy="13" r="9" stroke="#2563EB" stroke-width="2.2"/>
                <path d="M20 20l6 6" stroke="#2563EB" stroke-width="2.2" stroke-linecap="round"/>
                <circle cx="13" cy="13" r="4" fill="#EEF3FD" stroke="#2563EB" stroke-width="1.5"/>
            </svg>
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
    const raw = sessionStorage.getItem('rag_resultado');

    if (!raw) {
        mostrarError('No se encontró ningún resultado. Vuelve a iniciar la consulta.');
        return;
    }

    let data;
    try { data = JSON.parse(raw); } catch (e) { mostrarError('Respuesta inválida del servidor.'); return; }

    const r = data.resultado || {};

    // ── Doc header ────────────────────────────────────
    document.getElementById('res-tipo').textContent = r.documento_tipo || 'Documento electoral';
    document.getElementById('res-auth').textContent = r.autoridad     || '';

    // ── Badge ─────────────────────────────────────────
    const badge     = document.getElementById('res-badge');
    const urgencia  = r.urgencia || 'sin_plazo';
    const badgeMap  = {
        alta:     { cls: 'res-badge-alta',  txt: '● Actua Pronto'       },
        media:    { cls: 'res-badge-media', txt: '● Actua Esta Semana'  },
        baja:     { cls: 'res-badge-baja',  txt: '● Con Tiempo'         },
        sin_plazo:{ cls: 'res-badge-sin',   txt: '● Sin Plazo'          },
    };
    const bInfo = badgeMap[urgencia] || badgeMap['sin_plazo'];
    badge.classList.add(bInfo.cls);
    badge.textContent = bInfo.txt;

    // ── Urgency card ──────────────────────────────────
    const card = document.getElementById('res-urgency-card');
    card.classList.remove('alta', 'media', 'baja', 'sin');
    if (urgencia === 'media') card.classList.add('media');
    else if (urgencia === 'baja') card.classList.add('baja');
    else if (urgencia === 'sin_plazo') card.classList.add('sin');

    // Deadline title y barra
    const deadlineTitle = document.getElementById('res-deadline-title');
    const sub           = document.getElementById('res-deadline-sub');
    const bar           = document.getElementById('res-bar');

    if (r.fecha_limite_iso) {
        const hoy   = new Date(); hoy.setHours(0,0,0,0);
        const limit = new Date(r.fecha_limite_iso + 'T00:00:00');
        const dias  = Math.ceil((limit - hoy) / 86400000);

        deadlineTitle.textContent = r.fecha_limite_texto
            ? `Actua antes del ${r.fecha_limite_texto}`
            : `Actua antes del plazo`;

        if (dias < 0)        sub.textContent = 'El plazo ha vencido';
        else if (dias === 0) sub.textContent = 'Tienes menos de 1 día';
        else if (dias === 1) sub.textContent = 'Tienes 1 día';
        else                 sub.textContent = `Tienes ${dias} días`;

        const pct = urgencia === 'alta' ? 88 : urgencia === 'media' ? 58 : 28;
        bar.style.width = pct + '%';
    } else if (urgencia === 'sin_plazo') {
        deadlineTitle.textContent = r.documento_tipo || 'Documento electoral';
        sub.textContent           = 'Sin fecha límite identificada';
        bar.style.width           = '18%';
    } else {
        deadlineTitle.textContent = r.fecha_limite_texto
            ? `Actua antes del ${r.fecha_limite_texto}`
            : `Actua pronto`;
        sub.textContent           = '';
        bar.style.width           = urgencia === 'alta' ? '88%' : '55%';
    }

    // ── Info cards ────────────────────────────────────
    document.getElementById('res-porque').textContent      = r.por_que_lo_recibiste || '—';
    document.getElementById('res-consecuencias').textContent = r.consecuencias       || '—';

    const ol = document.getElementById('res-acciones');
    (r.acciones || []).forEach(a => {
        const li = document.createElement('li'); li.textContent = a; ol.appendChild(li);
    });
    if (!r.acciones || r.acciones.length === 0) {
        ol.innerHTML = '<li>Consulta directamente al TEE Chihuahua</li>';
    }

    // ── Mostrar contenido ─────────────────────────────
    document.getElementById('res-loading').style.display = 'none';
    document.getElementById('res-content').style.display = 'block';
})();

function mostrarError(msg) {
    document.getElementById('res-loading').style.display      = 'none';
    document.getElementById('res-error-msg').textContent      = msg;
    document.getElementById('res-error').style.display        = 'flex';
}
</script>
@endpush
