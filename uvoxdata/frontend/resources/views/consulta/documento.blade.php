@extends('layouts.app')

@section('shellClass', 'light-shell')

{{-- ── Header ──────────────────────────────────────── --}}
@section('header')
    <x-app-header :back="route('urgencia.documento')" title="Situación" />
@endsection

{{-- ── Content ─────────────────────────────────────── --}}
@section('content')

<div class="doc-bg"></div>
<div class="doc-wrap d-flex flex-column" style="min-height:100%;">

    {{-- Banner: muestra la selección del paso 1 si existe --}}
    <div id="selection-banner" class="selection-banner" hidden>
        <span class="selection-banner-label">Situación seleccionada</span>
        <span class="selection-banner-text" id="selection-text"></span>
    </div>

    {{-- Progress --}}
    <div class="mb-4">
        <x-progress :step="2" :total="3" />
    </div>

    {{-- Title --}}
    <div class="mb-4">
        <h1 class="lh-question">¿Tienes el documento<br>contigo?</h1>
        <p class="lh-subtitle">Esto nos ayuda a entender tu caso más rápido y con mayor precisión.</p>
    </div>

    {{-- Cards --}}
    <div class="d-flex flex-column gap-3">

        <x-option-card
            title="Escanear con cámara"
            subtitle="Toma una foto al documento"
            iconTone="default"
            :attrs="['id' => 'btn-camara']"
        >
            <x-slot:icon>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="2" y="7" width="20" height="14" rx="3.5" stroke="#2563EB" stroke-width="1.8"/>
                    <circle cx="12" cy="14" r="4" stroke="#2563EB" stroke-width="1.8"/>
                    <path d="M8 7l1.8-3h4.4L16 7" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            </x-slot:icon>
        </x-option-card>

        <x-option-card
            title="Subir archivo"
            subtitle="Selecciona un archivo PDF o imagen"
            iconTone="default"
            tag="label"
            :attrs="['for' => 'input-archivo']"
        >
            <x-slot:icon>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M14 2v6h6" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M12 18v-6M9 15l3-3 3 3" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </x-slot:icon>
        </x-option-card>

        <input type="file" id="input-archivo" accept=".pdf,image/*" class="d-none">

        <x-option-card
            title="No, describir la situación"
            subtitle="Escribe lo que entiendas del documento"
            iconTone="default"
            :href="route('consulta.descripcion')"
        >
            <x-slot:icon>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 20h-6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8l4 4v4" stroke="#2563EB" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M16 19l2 2 4-4" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 10h8M8 14h5" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </x-slot:icon>
        </x-option-card>

    </div>

    <div class="flex-grow-1"></div>

    <div class="text-center py-3">
        <a href="#" class="lh-help">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <circle cx="9" cy="9" r="8" stroke="#7C3AED" stroke-width="1.5"/>
                <path d="M9 13v-1" stroke="#7C3AED" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M6.5 7a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3" stroke="#7C3AED" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            ¿Qué documentos puedo subir?
        </a>
    </div>

</div>

{{-- ── Modal cámara ─────────────────────────────────── --}}
<div id="modal-camara" class="cam-modal" hidden>
    <div class="cam-modal-stage">
        <video id="cam-video" autoplay playsinline muted></video>
        <canvas id="cam-canvas" hidden></canvas>
    </div>

    <div class="cam-modal-controls">
        <button type="button" id="cam-cerrar" class="cam-btn" aria-label="Cerrar cámara">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M4 4l12 12M16 4L4 16" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

        <button type="button" id="cam-capturar" class="cam-btn cam-btn-shutter" aria-label="Capturar">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                <circle cx="14" cy="14" r="10" fill="#2563EB"/>
                <circle cx="14" cy="14" r="6" fill="white"/>
            </svg>
        </button>

        <button type="button" id="cam-voltear" class="cam-btn" aria-label="Voltear cámara">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                <path d="M4 11a7 7 0 0 1 13-3.5" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M18 11a7 7 0 0 1-13 3.5" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M17 4.5l-.5 3-3-.5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5 17.5l.5-3 3 .5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</div>

@push('styles')
<style>
/* Banner de selección heredada del paso 1 */
.selection-banner {
    position: relative;
    z-index: 2;
    margin: 0 0 14px;
    padding: 10px 14px;
    border-radius: 12px;
    background: #EEF3FD;
    border: 1px solid #C7D9F8;
    color: #1E3A8A;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.selection-banner-label {
    font-size: 11px;
    font-weight: 600;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.selection-banner-text {
    font-size: 13px;
    font-weight: 600;
    color: #1E293B;
}

/* Modal cámara */
.cam-modal {
    display: flex;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0,0,0,0.92);
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.cam-modal[hidden] { display: none; }

.cam-modal-stage {
    position: relative;
    width: 100%;
    max-width: 390px;
}

.cam-modal-stage video {
    width: 100%;
    border-radius: 16px;
    display: block;
    background: #000;
}

.cam-modal-controls {
    display: flex;
    gap: 24px;
    margin-top: 24px;
    align-items: center;
}

.cam-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.cam-btn-shutter {
    background: #fff;
    width: 72px;
    height: 72px;
    box-shadow: 0 0 0 5px rgba(255,255,255,0.3);
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    /* ────────── Selection banner ────────── */
    const selection = sessionStorage.getItem('modo_seleccionado');
    if (selection) {
        const banner = document.getElementById('selection-banner');
        document.getElementById('selection-text').textContent = selection;
        banner.hidden = false;
    }

    /* ────────── Cámara (getUserMedia) ────────── */
    const modal = document.getElementById('modal-camara');
    const video = document.getElementById('cam-video');
    const canvas = document.getElementById('cam-canvas');
    let stream = null;
    let facingMode = 'environment';

    async function abrirCamara() {
        try {
            if (stream) cerrarCamara();
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode, width: { ideal: 1920 }, height: { ideal: 1080 } },
                audio: false,
            });
            video.srcObject = stream;
            modal.hidden = false;
        } catch (err) {
            alert('No se pudo acceder a la cámara. Verifica los permisos del navegador.');
            console.error(err);
        }
    }

    function cerrarCamara() {
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        video.srcObject = null;
        modal.hidden = true;
    }

    function capturarFoto() {
        canvas.width = video.videoWidth;
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

    /* ────────── Subir archivo ────────── */
    document.getElementById('input-archivo').addEventListener('change', function () {
        enviarArchivo(this.files[0]);
    });

    /* ────────── Envío al backend (Laravel → FastAPI / RAG) ────────── */
    async function enviarArchivo(file) {
        if (!file) return;

        if (!navigator.onLine) {
            alert('Sin conexión no podemos enviar el archivo al servidor. Conéctate a internet o elige “No, describir la situación” para una orientación offline.');
            return;
        }

        const allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) {
            alert('Solo se aceptan archivos PDF o imágenes (JPG, PNG, WEBP).');
            return;
        }

        document.querySelectorAll('.lh-card').forEach(c => c.style.opacity = '0.5');
        sessionStorage.removeItem('rag_resultado');

        const formData = new FormData();
        formData.append('archivo', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const res = await fetch('{{ route("consulta.subir") }}', { method: 'POST', body: formData });
            let body = null;
            const raw = await res.text();
            if (raw) {
                try { body = JSON.parse(raw); } catch (_) { body = { _raw: raw }; }
            }
            if (!res.ok) {
                const apiMsg = body?.error || body?.message;
                const hint =
                    res.status === 503
                        ? ' Arranca FastAPI y revisa BACKEND_URL en .env (por defecto http://127.0.0.1:8001).'
                        : '';
                throw new Error(
                    (apiMsg ? apiMsg + ' ' : '') +
                        `(HTTP ${res.status}).` +
                        hint
                );
            }
            if (!body || typeof body !== 'object') {
                throw new Error('Respuesta vacía o no JSON del servidor.');
            }
            sessionStorage.setItem('rag_resultado', JSON.stringify(body));
            window.location.href = '{{ route("consulta.procesando") }}';
        } catch (err) {
            document.querySelectorAll('.lh-card').forEach(c => c.style.opacity = '1');
            const msg =
                err && err.message
                    ? err.message
                    : 'No se pudo analizar el documento. Revisa la consola (F12) y BACKEND_URL.';
            alert(msg);
            console.error(err);
        }
    }
})();
</script>
@endpush

@endsection
