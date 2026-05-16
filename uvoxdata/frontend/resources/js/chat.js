import { consultarBackend } from './api';

const MAX_CONTEXT_CHARS = 6000;
const MAX_TURNS = 8;

function buildContextoFromHistory(history) {
    if (!history.length) return null;
    const chunk = history
        .slice(-MAX_TURNS)
        .map((h) => `${h.role === 'user' ? 'Usuario' : 'OrientaVox'}: ${h.text}`)
        .join('\n');
    return chunk.length > MAX_CONTEXT_CHARS
        ? chunk.slice(-MAX_CONTEXT_CHARS)
        : chunk;
}

function formatConsultaReply(data) {
    if (data?.necesita_aclaracion) {
        const opts = (data.opciones || []).filter(Boolean);
        const q = data.pregunta_aclaracion || 'Necesitamos un poco más de contexto.';
        return opts.length ? `${q}\n\nOpciones: ${opts.join(' | ')}` : q;
    }
    const body = (data?.respuesta || '').trim();
    const fuentes = Array.isArray(data?.fuentes) ? data.fuentes.filter(Boolean) : [];
    const modo = data?.modo === 'degradado' ? ' (modo degradado)' : '';
    const src = fuentes.length ? `\n\nFuentes: ${fuentes.join('; ')}` : '';
    return `${body}${modo}${src}`.trim() || 'Sin respuesta del servidor.';
}

/**
 * Chat conversacional en /consulta/chat: reutiliza POST /consulta vía Laravel con historial en `contexto`.
 */
export function mountConsultaChat() {
    const thread = document.getElementById('chat-thread');
    const form = document.getElementById('chat-consulta-form');
    const input = document.getElementById('chat-input');
    const offlineHint = document.getElementById('chat-offline-hint');
    if (!thread || !form || !input) return;

    const history = [];

    function setOfflineHint() {
        if (!offlineHint) return;
        offlineHint.hidden = navigator.onLine;
    }

    function appendMessage(role, text) {
        const wrap = document.createElement('div');
        wrap.className = `chat-msg chat-msg--${role}`;
        const bubble = document.createElement('div');
        bubble.className = 'chat-msg-bubble';
        bubble.textContent = text;
        wrap.appendChild(bubble);
        thread.appendChild(wrap);
        thread.scrollTop = thread.scrollHeight;
        return wrap;
    }

    window.addEventListener('online', setOfflineHint);
    window.addEventListener('offline', setOfflineHint);
    setOfflineHint();

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        setOfflineHint();

        appendMessage('user', text);
        history.push({ role: 'user', text });

        const pending = appendMessage('assistant', '…');

        const ctx = buildContextoFromHistory(history.slice(0, -1));
        try {
            const data = await consultarBackend(text, ctx);
            pending.remove();
            const reply = formatConsultaReply(data);
            appendMessage('assistant', reply);
            history.push({ role: 'assistant', text: reply });
        } catch (err) {
            pending.remove();
            appendMessage(
                'assistant',
                'No se pudo completar la consulta. Revisa conexión y BACKEND_URL.'
            );
            console.error(err);
        }
    });
}
