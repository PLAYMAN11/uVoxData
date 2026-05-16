/**
 * mountLupa — Sprite sheet de Lupin por canvas (default 5×5, procesando 6×6).
 *
 *   <div id="x" class="lupa-stage lupa-stage--cta"><svg>...</svg></div>
 *   mountLupa(el, { variant: 'default' });
 *
 * URLs: usa `window.OrientaVox.lupinDefaultSrc` / `lupinProcessingSrc` (definidas
 * en el layout con `asset()`) y cae a `/assets/...` si no existen.
 *
 * Si omites `size`, el canvas sigue el tamaño del contenedor (ResizeObserver).
 * Ciclo del sprite: 5 s. Clase .lupa-stage--bob en el contenedor.
 */

const LUPIN_CYCLE_MS = 5000;

const VARIANT_PRESET = {
    default: {
        cols: 5,
        frames: 25,
        src: '/assets/lupin-default.png',
    },
    processing: {
        cols: 6,
        frames: 36,
        src: '/assets/lupin-processing.png',
    },
};

const BOB_CLASS = 'lupa-stage--bob';

function resolveSpriteSrc(variant, preset, optsSrc) {
    if (optsSrc) return optsSrc;
    if (typeof window !== 'undefined' && window.OrientaVox) {
        const o = window.OrientaVox;
        if (variant === 'processing' && o.lupinProcessingSrc) return o.lupinProcessingSrc;
        if (variant === 'default' && o.lupinDefaultSrc) return o.lupinDefaultSrc;
    }
    return preset.src;
}

export function mountLupa(container, opts = {}) {
    if (!container) return () => {};

    const variant = opts.variant === 'processing' ? 'processing' : 'default';
    const preset = VARIANT_PRESET[variant];

    const {
        size: explicitSizeOpt,
        cols = preset.cols,
        frames = preset.frames,
        src: optsSrc,
        cycleMs = LUPIN_CYCLE_MS,
    } = opts;

    const src = resolveSpriteSrc(variant, preset, optsSrc);
    const explicitSize = typeof explicitSizeOpt === 'number' ? explicitSizeOpt : null;
    const frameMs = cycleMs / frames;

    container.classList.add(BOB_CLASS);

    const canvas = document.createElement('canvas');
    canvas.style.cssText = 'position:absolute;inset:0;margin:auto;display:block;z-index:2;pointer-events:none;';
    container.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    if (ctx) {
        ctx.imageSmoothingEnabled = true;
        try {
            if ('imageSmoothingQuality' in ctx) ctx.imageSmoothingQuality = 'high';
        } catch (e) { /* IE / motores antiguos */ }
    }

    const img = new Image();
    // Misma origen que la página (asset()); evita tainted canvas si algún día hay CORS
    img.decoding = 'async';

    let raf = 0;
    let frame = 0;
    let last = 0;
    let fw = 0;
    let fh = 0;
    let sizePx = explicitSize ?? 64;
    let ro = null;
    let started = false;
    let imgLoaded = false;

    function readSizeFromContainer() {
        if (explicitSize != null) return explicitSize;
        const w = container.clientWidth;
        const h = container.clientHeight;
        const s = Math.round(Math.min(w, h));
        return Math.max(40, s || 64);
    }

    function applyCanvasSize() {
        const next = readSizeFromContainer();
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const bw = Math.max(1, Math.round(next * dpr));
        const bh = Math.max(1, Math.round(next * dpr));
        if (sizePx === next && canvas.width === bw && canvas.height === bh) return;
        sizePx = next;
        canvas.width = bw;
        canvas.height = bh;
        canvas.style.width = `${sizePx}px`;
        canvas.style.height = `${sizePx}px`;
        if (ctx) {
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }
    }

    function tick(now) {
        if (!ctx || !fw) return;
        if (now - last >= frameMs) {
            frame = (frame + 1) % frames;
            last = now;
        }
        const col = frame % cols;
        const row = Math.floor(frame / cols);
        ctx.clearRect(0, 0, sizePx, sizePx);
        ctx.drawImage(img, col * fw, row * fh, fw, fh, 0, 0, sizePx, sizePx);
        raf = requestAnimationFrame(tick);
    }

    function startAnimation() {
        if (started) return;
        started = true;
        applyCanvasSize();
        container.classList.add('has-canvas');
        last = performance.now();
        raf = requestAnimationFrame(tick);
    }

    function onImgReady() {
        if (imgLoaded) return;
        imgLoaded = true;

        fw = img.naturalWidth / cols;
        fh = img.naturalHeight / Math.ceil(frames / cols);
        if (!fw || !fh || !ctx) {
            canvas.remove();
            return;
        }

        if (explicitSize == null) {
            ro = new ResizeObserver(() => {
                applyCanvasSize();
            });
            ro.observe(container);
        }

        requestAnimationFrame(() => {
            applyCanvasSize();
            startAnimation();
        });
    }

    img.addEventListener(
        'error',
        () => {
            if (imgLoaded) return;
            imgLoaded = true;
            canvas.remove();
        },
        { once: true },
    );

    img.addEventListener('load', onImgReady, { once: true });
    img.src = src;

    // Imagen en caché: el evento "load" puede haberse disparado antes de registrar el listener
    if (img.complete && img.naturalWidth > 0) {
        queueMicrotask(onImgReady);
    }

    return function unmount() {
        if (ro) {
            ro.disconnect();
            ro = null;
        }
        cancelAnimationFrame(raf);
        canvas.remove();
        container.classList.remove('has-canvas', BOB_CLASS);
    };
}

if (typeof window !== 'undefined') {
    window.OrientaVox = window.OrientaVox || {};
    window.OrientaVox.mountLupa = mountLupa;
}
