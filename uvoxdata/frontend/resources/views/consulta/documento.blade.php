@extends('layouts.app')

@section('shellClass', 'light-shell')

{{-- ── Custom header ──────────────────────────────── --}}
@section('header')

<div class="lh-header w-100 d-flex align-items-center justify-content-between px-3">

    <a href="{{ route('home') }}" class="lh-back d-flex align-items-center">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <path d="M13 5l-6 6 6 6" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <span class="lh-title" > Situación
    </span>

    <button class="lh-menu-btn" aria-label="Menú">
        <svg width="18" height="14" viewBox="0 0 18 14" fill="none">
            <path d="M1 1h16M1 7h16M1 13h16" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>

</div>
@endsection

{{-- ── Inline styles ────────────────────────────────── --}}
@push('styles')
<style>
/* Light shell overrides */
.light-shell { background: #FFFFFF !important; }
.light-shell .app-header { background: #FFFFFF; border-bottom: 1px solid #E9EEF6; padding-top: 16px; padding-bottom: 16px; height: auto; min-height: 56px; }
.light-shell .app-main   { background: #FFFFFF; padding: 28px 20px 20px; position: relative; }
.light-shell .app-bottom { background: #FFFFFF; border-top: 1px solid #E9EEF6; }

/* Header */
.lh-header { height: 100%; }
.lh-back   { color: #1E293B; text-decoration: none; }
.lh-title  { font-size: 17px; font-weight: 700; color: #1E293B; letter-spacing: -0.01em; }
.lh-menu-btn {
    background: #FFFFFF;
    border: 1.5px solid #D1D9E6;
    border-radius: 10px;
    width: 38px; height: 38px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    padding: 0;
}
.lh-menu-btn:hover { background: #F1F5F9; }

/* ── Background decoration circles ── */
.doc-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: 0;
}
.doc-bg::before {
    content: '';
    position: absolute;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, #C7D9F8 0%, transparent 70%);
    top: -80px; right: -100px;
    opacity: 0.45;
}
.doc-bg::after {
    content: '';
    position: absolute;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, #D4E4FB 0%, transparent 70%);
    bottom: 40px; right: -60px;
    opacity: 0.35;
}
.doc-wrap {
    position: relative;
    z-index: 1;
    width: 100%;
}

/* ── Step / progress ── */
.lh-step-label {
    font-size: 12px;
    font-weight: 500;
    color: #64748B;
    letter-spacing: 0.01em;
}
.lh-progress-track {
    height: 6px;
    background: #E2E8F0;
    border-radius: 99px;
    overflow: hidden;
}
.lh-progress-fill {
    height: 100%;
    background: #2F77E2;
    border-radius: 99px;
}

/* ── Title / subtitle ── */
.lh-question {
    font-size: 28px;
    font-weight: 800;
    color: #0F1D35;
    line-height: 1.25;
    letter-spacing: -0.02em;
}
.lh-subtitle {
    font-size: 14px;
    color: #64748B;
    line-height: 1.55;
}

/* ── Option cards ── */
.lh-card {
    background: #FFFFFF;
    border: 1.5px solid #E2EAF4;
    border-radius: 16px;
    padding: 16px 14px;
    display: flex;
    align-items: center;
    gap: 14px;
    text-decoration: none;
    cursor: pointer;
    transition: border-color .15s, box-shadow .15s, background .15s;
    width: 100%;
}
.lh-card:hover, .lh-card:active {
    border-color: #2F77E2;
    box-shadow: 0 0 0 3px rgba(47,111,232,.08);
    background: #E7EDFE;
}
.lh-card-icon {
    width: 48px; height: 48px;
    background: #E7EDFE;
    border-radius: 13px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.lh-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #2F77E2;
    margin: 0 0 2px;
    line-height: 1.2;
}
.lh-card-sub {
    font-size: 12px;
    color: #94A3B8;
    margin: 0;
}
.lh-chevron { flex-shrink: 0; margin-left: auto; }

/* ── Bottom help link ── */
.lh-help {
    font-size: 13px;
    color: #64748B;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}
.lh-help:hover { color: #2F77E2; }
</style>
@endpush

{{-- ── Content ─────────────────────────────────────── --}}
@section('content')

<div class="doc-bg"></div>
<div class="doc-wrap d-flex flex-column" style="min-height:100%; gap:0;">

    {{-- Step --}}
    <div class="mb-4">
        <p class="lh-step-label mb-2">Paso 2 de 3</p>
        <div class="lh-progress-track">
            <div class="lh-progress-fill" style="width:66.6%"></div>
        </div>
    </div>

    {{-- Title --}}
    <div class="mb-4">
        <h1 class="lh-question mb-2">¿Tienes el documento<br>contigo?</h1>
        <p class="lh-subtitle mb-0">Esto nos ayuda a entender tu caso más rápido y con mayor precisión.</p>
    </div>

    {{-- Cards --}}
    <div class="d-flex flex-column gap-3">

        {{-- Cámara --}}
        <button type="button" class="lh-card" id="btn-camara">
            <div class="lh-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="7" width="20" height="14" rx="3.5" stroke="#2563EB" stroke-width="1.8"/>
                    <circle cx="12" cy="14" r="4" stroke="#2563EB" stroke-width="1.8"/>
                    <path d="M8 7l1.8-3h4.4L16 7" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="flex-grow-1 text-start">
                <p class="lh-card-title">Escanear con cámara</p>
                <p class="lh-card-sub">Toma una foto al documento</p>
            </div>
            <svg class="lh-chevron" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M6 4l4 4-4 4" stroke="#CBD5E1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        {{-- Subir archivo --}}
        <label class="lh-card" for="input-archivo">
            <div class="lh-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M14 2v6h6" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M12 18v-6M9 15l3-3 3 3" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="flex-grow-1 text-start">
                <p class="lh-card-title">Subir archivo</p>
                <p class="lh-card-sub">Selecciona un archivo PDF</p>
            </div>
            <svg class="lh-chevron" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M6 4l4 4-4 4" stroke="#CBD5E1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </label>
        <input type="file" id="input-archivo" accept=".pdf,image/*" class="d-none">

        {{-- No tengo el documento --}}
        <a href="{{ route('consulta.descripcion') }}" class="lh-card">
            <div class="lh-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M12 20h-6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8l4 4v4" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M16 19l2 2 4-4" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 10h8M8 14h5" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="flex-grow-1 text-start">
                <p class="lh-card-title">No, describir la situación</p>
                <p class="lh-card-sub">Escribe lo que entiendas del documento</p>
            </div>
            <svg class="lh-chevron" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M6 4l4 4-4 4" stroke="#CBD5E1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

    </div>

    {{-- Spacer --}}
    <div class="flex-grow-1"></div>

    {{-- Help link --}}
    <div class="mt-auto d-flex justify-content-center py-3">
        <a href="#" class="lh-help d-flex align-items-center gap-2">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <circle cx="9" cy="9" r="8" stroke="#7C3AED" stroke-width="1.5"/>
                <path d="M9 13v-1" stroke="#7C3AED" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M6.5 7a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3" stroke="#7C3AED" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span>¿Qué documentos puedo subir?</span>
        </a>
    </div>

</div>

{{-- ── Modal de cámara ─────────────────────────────── --}}
<div id="modal-camara" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.92);
    flex-direction:column; align-items:center; justify-content:center;
">
    <div style="position:relative; width:100%; max-width:390px;">
        <video id="cam-video" autoplay playsinline muted
               style="width:100%; border-radius:16px; display:block; background:#000;"></video>
        <canvas id="cam-canvas" style="display:none;"></canvas>
    </div>

    <div style="display:flex; gap:24px; margin-top:24px; align-items:center;">
        {{-- Cerrar --}}
        <button id="cam-cerrar" style="
            background:rgba(255,255,255,0.15); border:none; border-radius:50%;
            width:48px; height:48px; display:flex; align-items:center; justify-content:center; cursor:pointer;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M4 4l12 12M16 4L4 16" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

        {{-- Capturar --}}
        <button id="cam-capturar" style="
            background:white; border:none; border-radius:50%;
            width:72px; height:72px; display:flex; align-items:center; justify-content:center; cursor:pointer;
            box-shadow: 0 0 0 5px rgba(255,255,255,0.3);">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                <circle cx="14" cy="14" r="10" fill="#2563EB"/>
                <circle cx="14" cy="14" r="6" fill="white"/>
            </svg>
        </button>

        {{-- Voltear cámara --}}
        <button id="cam-voltear" style="
            background:rgba(255,255,255,0.15); border:none; border-radius:50%;
            width:48px; height:48px; display:flex; align-items:center; justify-content:center; cursor:pointer;">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
                <path d="M4 11a7 7 0 0 1 13-3.5" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M18 11a7 7 0 0 1-13 3.5" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M17 4.5l-.5 3-3-.5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5 17.5l.5-3 3 .5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════════════
// CÁMARA (getUserMedia)
// ══════════════════════════════════════════════════
const modal     = document.getElementById('modal-camara');
const video     = document.getElementById('cam-video');
const canvas    = document.getElementById('cam-canvas');
let stream      = null;
let facingMode  = 'environment'; // trasera por defecto

async function abrirCamara() {
    try {
        if (stream) cerrarCamara();
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode, width: { ideal: 1920 }, height: { ideal: 1080 } },
            audio: false,
        });
        video.srcObject = stream;
        modal.style.display = 'flex';
    } catch (err) {
        alert('No se pudo acceder a la cámara. Verifica los permisos del navegador.');
        console.error(err);
    }
}

function cerrarCamara() {
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    video.srcObject = null;
    modal.style.display = 'none';
}

function capturarFoto() {
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    canvas.toBlob(blob => {
        cerrarCamara();
        const file = new File([blob], 'foto.jpg', { type: 'image/jpeg' });
        enviarArchivo(file);
    }, 'image/jpeg', 0.92);
}

document.getElementById('btn-camara').addEventListener('click', abrirCamara);
document.getElementById('cam-cerrar').addEventListener('click', cerrarCamara);
document.getElementById('cam-capturar').addEventListener('click', capturarFoto);
document.getElementById('cam-voltear').addEventListener('click', () => {
    facingMode = facingMode === 'environment' ? 'user' : 'environment';
    abrirCamara();
});

// ══════════════════════════════════════════════════
// SUBIR ARCHIVO
// ══════════════════════════════════════════════════
document.getElementById('input-archivo').addEventListener('change', function () {
    enviarArchivo(this.files[0]);
});

// ══════════════════════════════════════════════════
// ENVIAR AL BACKEND
// ══════════════════════════════════════════════════
function enviarArchivo(file) {
    if (!file) return;

    const allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    if (!allowed.includes(file.type)) {
        alert('Solo se aceptan archivos PDF o imágenes (JPG, PNG, WEBP).');
        return;
    }

    document.querySelectorAll('.lh-card').forEach(c => c.style.opacity = '0.5');

    const formData = new FormData();
    formData.append('archivo', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    fetch('{{ route("consulta.subir") }}', { method: 'POST', body: formData })
        .then(res => {
            if (!res.ok) return res.json().then(e => Promise.reject(e));
            return res.json();
        })
        .then(data => {
            sessionStorage.setItem('rag_resultado', JSON.stringify(data));
            window.location.href = '{{ route("consulta.resultado") }}';
        })
        .catch(err => {
            document.querySelectorAll('.lh-card').forEach(c => c.style.opacity = '1');
            alert('Error al procesar el archivo. Intenta de nuevo.');
            console.error(err);
        });
}
</script>
@endpush
