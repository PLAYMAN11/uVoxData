@extends('layouts.app')

@section('shellClass', 'light-shell chat-shell')

@section('header')
    <x-app-header :back="route('consulta.resultado')" title="Más información" />
@endsection

@section('content')

<div class="chat-screen">

    <div class="chat-scroll">

        {{-- Lupin arriba a la izquierda + texto guía --}}
        <div class="chat-guide">
            <div id="lupin-chat" class="lupa-stage lupa-stage--chat">
                <svg viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="18" cy="18" r="12" fill="#E8EEFF" stroke="#4A7CF7" stroke-width="2"/>
                    <ellipse cx="18" cy="18" rx="6" ry="4" fill="#fff"/>
                    <circle cx="18" cy="18" r="2.5" fill="#2A50C8"/>
                    <circle cx="19.2" cy="16.8" r="0.9" fill="#fff"/>
                    <line x1="27" y1="27" x2="34" y2="34" stroke="#6A3FC8" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="chat-guide-text">
                <p class="chat-guide-caption">¿Dónde requieres mayor información?</p>
            </div>
        </div>

        {{-- 5 cards expandibles (contenido desde sessionStorage) --}}
        <div class="chat-topics" id="chat-topics" role="list"></div>

        <p id="chat-offline-hint" class="chat-offline-hint" hidden>
            Sin conexión: las respuestas usan el catálogo local (sin RAG en vivo).
        </p>

        <section class="chat-ai-panel" aria-label="Chat con OrientaVox">
            <h2 class="chat-ai-title">Sigue la conversación</h2>
            <p class="chat-ai-sub">
                Escribe con tus palabras. Con red, cada mensaje va al mismo análisis que la demo (RAG + documentos oficiales).
            </p>
            <div id="chat-thread" class="chat-thread" aria-live="polite"></div>
            <form id="chat-consulta-form" class="chat-consulta-form">
                <label class="visually-hidden" for="chat-input">Tu pregunta</label>
                <textarea
                    id="chat-input"
                    class="chat-input"
                    rows="2"
                    maxlength="2000"
                    placeholder="Ej. ¿Qué pasa si no contesto a tiempo?"
                    required
                ></textarea>
                <button type="submit" class="chat-consulta-send">Enviar</button>
            </form>
        </section>
    </div>

    <div class="chat-foot" role="navigation" aria-label="Acciones">
        <a href="{{ route('consulta.documento') }}" class="chat-foot-btn chat-foot-btn--primary">
            Subir otro documento
        </a>
        <a href="{{ route('home') }}" class="chat-foot-btn chat-foot-btn--secondary">
            Regresar
        </a>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const raw = sessionStorage.getItem('rag_resultado');
    if (!raw) {
        window.location.href = '{{ route('home') }}';
        return;
    }

    let data;
    try { data = JSON.parse(raw); }
    catch (e) {
        window.location.href = '{{ route('home') }}';
        return;
    }

    const r = data.resultado || {};
    const topicsEl = document.getElementById('chat-topics');

    /* Lupin — sprite con fallback (mismo patrón que resultado/procesando) */
    function bootLupin() {
        const mount = window.OrientaVox?.mountLupa;
        const el = document.getElementById('lupin-chat');
        if (mount && el) mount(el, { variant: 'default' });
        else window.addEventListener('load', function once() {
            window.removeEventListener('load', once);
            window.OrientaVox?.mountLupa?.(el, { variant: 'default' });
        });
    }
    bootLupin();

    function diasRestantes() {
        if (!r.fecha_limite_iso) return null;
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        const lim = new Date(r.fecha_limite_iso + 'T00:00:00');
        return Math.ceil((lim - hoy) / 86400000);
    }

    function appendParagraph(root, text) {
        if (!text) return;
        const p = document.createElement('p');
        p.textContent = text;
        root.appendChild(p);
    }

    function buildTopicBodies() {
        const dias = diasRestantes();

        const docAuth = document.createElement('div');
        const tipo = (r.documento_tipo || '').trim();
        const aut = (r.autoridad || '').trim();
        if (tipo) appendParagraph(docAuth, 'Documento: ' + tipo);
        else appendParagraph(docAuth, 'No tenemos el nombre exacto del documento en este resumen. Revísalo en el encabezado del papel que recibiste.');
        if (aut) appendParagraph(docAuth, 'Autoridad competente: ' + aut);
        else appendParagraph(docAuth, 'La autoridad no aparece en el resumen. Suele indicarse al inicio o al final del documento (remitente, sello o firma).');

        const plazos = document.createElement('div');
        if (!r.fecha_limite_iso) {
            appendParagraph(plazos, 'En este resumen no aparece una fecha límite clara. Busca en el documento palabras como “plazo”, “término” o una fecha concreta.');
        } else {
            const cuando = r.fecha_limite_texto ? r.fecha_limite_texto : 'la fecha indicada en el sistema';
            appendParagraph(plazos, 'La fecha límite que tenemos es: ' + cuando + '.');
            let frase;
            if (dias < 0) frase = 'Ese plazo ya pasó. Aun así puedes acercarte a la autoridad para saber si aún puedes hacer algo.';
            else if (dias === 0) frase = 'Queda muy poco tiempo (menos de un día). Te conviene actuar hoy.';
            else if (dias === 1) frase = 'Te queda 1 día para atenderlo.';
            else frase = 'Te quedan aproximadamente ' + dias + ' días para atenderlo.';
            appendParagraph(plazos, frase);
        }

        const significa = document.createElement('div');
        appendParagraph(
            significa,
            (r.por_que_lo_recibiste || '').trim()
                || 'Aquí no tenemos una explicación detallada. En general, este tipo de documentos llega porque estás involucrado en un trámite o notificación oficial.'
        );

        const riesgos = document.createElement('div');
        appendParagraph(
            riesgos,
            (r.consecuencias || '').trim()
                || 'Si no das seguimiento, podrías perder derechos o recibir consecuencias administrativas según el tipo de trámite. Lo importante es no ignorarlo.'
        );

        const soluciones = document.createElement('div');
        const acciones = Array.isArray(r.acciones) && r.acciones.length
            ? r.acciones
            : [
                'Lee el documento con calma y subraya fechas y requisitos.',
                'Acude o llama a la autoridad que lo emitió si tienes dudas.',
                'Junta las pruebas o papeles que te pidan y guarda copia de todo.',
            ];
        appendParagraph(soluciones, 'Pasos prácticos que puedes seguir:');
        const ul = document.createElement('ul');
        acciones.forEach(function (item) {
            const li = document.createElement('li');
            li.textContent = item;
            ul.appendChild(li);
        });
        soluciones.appendChild(ul);

        return [
            { title: 'Documento y autoridad competente', body: docAuth },
            { title: 'Plazos', body: plazos },
            { title: 'Qué significa', body: significa },
            { title: 'Qué riesgos hay si no lo atiendo', body: riesgos },
            { title: 'Soluciones', body: soluciones },
        ];
    }

    function renderTopics() {
        topicsEl.innerHTML = '';
        const items = buildTopicBodies();
        const sections = [];

        items.forEach(function (item, index) {
            const sec = document.createElement('section');
            sec.className = 'chat-topic';
            sec.setAttribute('role', 'listitem');
            sec.dataset.open = 'false';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'chat-topic-head';
            btn.setAttribute('aria-expanded', 'false');
            btn.setAttribute('id', 'chat-topic-head-' + index);
            btn.setAttribute('aria-controls', 'chat-topic-panel-' + index);

            const title = document.createElement('span');
            title.className = 'chat-topic-title';
            title.textContent = item.title;

            const chev = document.createElement('span');
            chev.className = 'chat-topic-chevron';
            chev.setAttribute('aria-hidden', 'true');
            chev.innerHTML = '<svg width="14" height="8" viewBox="0 0 14 8" fill="none"><path d="M1 1l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

            btn.appendChild(title);
            btn.appendChild(chev);

            const panel = document.createElement('div');
            panel.className = 'chat-topic-panel';
            panel.id = 'chat-topic-panel-' + index;

            const inner = document.createElement('div');
            inner.className = 'chat-topic-panel-inner';

            const body = document.createElement('div');
            body.className = 'chat-topic-body';
            body.id = 'chat-topic-body-' + index;
            body.setAttribute('role', 'region');
            body.setAttribute('aria-labelledby', 'chat-topic-head-' + index);
            while (item.body.firstChild) {
                body.appendChild(item.body.firstChild);
            }

            inner.appendChild(body);
            panel.appendChild(inner);
            sec.appendChild(btn);
            sec.appendChild(panel);
            topicsEl.appendChild(sec);
            sections.push(sec);

            btn.addEventListener('click', function () {
                const isOpen = sec.dataset.open === 'true';
                sections.forEach(function (s) {
                    s.dataset.open = 'false';
                    const b = s.querySelector('.chat-topic-head');
                    if (b) b.setAttribute('aria-expanded', 'false');
                });
                if (!isOpen) {
                    sec.dataset.open = 'true';
                    btn.setAttribute('aria-expanded', 'true');
                }
            });
        });
    }

    renderTopics();
})();
</script>
@endpush

@endsection
