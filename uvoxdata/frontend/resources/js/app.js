import './connectivity';
import { consultarBackend, subirDocumento } from './api';

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js');
}

const form = document.getElementById('consulta-form');
const textarea = document.getElementById('pregunta');
const charCount = document.getElementById('char-count');

textarea?.addEventListener('input', () => {
    charCount.textContent = `${textarea.value.length} / 2000`;
});

form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const pregunta = textarea.value.trim();
    if (!pregunta) return;

    const data = await consultarBackend(pregunta);
    renderRespuesta(data);
});

document.getElementById('upload-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const file = document.getElementById('file-input').files[0];
    if (!file) return;

    const status = document.getElementById('upload-status');
    status.textContent = 'Indexando...';
    const result = await subirDocumento(file);
    status.textContent = result.mensaje ?? 'Listo.';
});

function renderRespuesta(data) {
    const container = document.getElementById('respuesta-container');
    const aclaracion = document.getElementById('aclaracion-container');

    if (data.necesita_aclaracion) {
        document.getElementById('clarification-question').textContent = data.pregunta_aclaracion;
        const opts = document.getElementById('clarification-options');
        opts.innerHTML = '';
        (data.opciones ?? []).forEach((op) => {
            const btn = document.createElement('button');
            btn.className = 'bg-yellow-200 text-yellow-900 px-3 py-1 rounded-full text-xs hover:bg-yellow-300';
            btn.textContent = op;
            btn.addEventListener('click', () => consultarBackend(op).then(renderRespuesta));
            opts.appendChild(btn);
        });
        aclaracion.classList.remove('hidden');
        container.classList.add('hidden');
        return;
    }

    aclaracion.classList.add('hidden');

    const badge = document.getElementById('response-mode-badge');
    badge.textContent = data.modo === 'degradado' ? 'Modo degradado' : 'Respuesta completa';
    badge.className = `text-xs px-2 py-1 rounded-full font-medium ${
        data.modo === 'degradado'
            ? 'bg-orange-100 text-orange-700'
            : 'bg-green-100 text-green-700'
    }`;

    document.getElementById('response-text').textContent = data.respuesta;

    const sources = document.getElementById('response-sources');
    sources.innerHTML = (data.fuentes ?? [])
        .map((f) => `<p>Fuente: ${f}</p>`)
        .join('');

    container.classList.remove('hidden');
}
