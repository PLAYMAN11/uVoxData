{{--
=======================================================================
  PROCESANDO.BLADE.PHP
=======================================================================

  CÓMO CONECTAR EL BACKEND (Laravel 12 / PHP 8.2):

  OPCIÓN A — POLLING (recomendada, sin dependencias extra)
  ─────────────────────────────────────────────────────────
  1. El controlador lanza un Job en cola y guarda el progreso en Cache:

     // EmergenciaController.php
     public function procesar(Request $request)
     {
         $jobId = Str::uuid();
         Cache::put("job_progress_{$jobId}", 0, now()->addMinutes(5));
         ProcesarDocumentoJob::dispatch($jobId, $request->all());
         return view('procesando', compact('jobId'));
     }

  2. El Job actualiza el progreso en Cache conforme avanza:

     // ProcesarDocumentoJob.php
     public function handle()
     {
         Cache::put("job_progress_{$this->jobId}", 25);   // paso 1 listo
         // ... lógica ...
         Cache::put("job_progress_{$this->jobId}", 50);   // paso 2 listo
         // ...
         Cache::put("job_progress_{$this->jobId}", 75);   // paso 3 listo
         // ...
         Cache::put("job_progress_{$this->jobId}", 100);  // todo listo
         Cache::put("job_done_{$this->jobId}", route('resultado', $this->jobId), now()->addMinutes(5));
     }

  3. Expón un endpoint JSON que el JS consulta cada ~800ms:

     // routes/web.php
     Route::get('/emergencia/progreso/{jobId}', function ($jobId) {
         return response()->json([
             'progress' => Cache::get("job_progress_{$jobId}", 0),
             'done'     => Cache::has("job_done_{$jobId}"),
             'redirect' => Cache::get("job_done_{$jobId}"),
         ]);
     })->name('emergencia.progreso');

  4. La vista recibe $jobId via Blade y el JS hace polling a ese endpoint.
     Cuando done=true, redirige automáticamente con un breve delay.

  OPCIÓN B — LARAVEL REVERB / BROADCASTING (WebSockets)
  ──────────────────────────────────────────────────────
  - Emite JobProgressUpdated event con $progress desde el Job.
  - El frontend escucha el canal privado con Echo + Reverb.
  - Más complejo pero actualización instantánea sin polling.

  NOTA SOBRE LAS "PALOMAS" (checkmarks):
  ──────────────────────────────────────
  Son 100% CSS/JS hardcodeadas. Se activan por umbrales de la barra:
    ≥  1% → spinner en "Revisando documento"
    ≥ 25% → ✓ Revisando documento  +  spinner en "Identificando..."
    ≥ 50% → ✓ Identificando...     +  spinner en "Consultando..."
    ≥ 75% → ✓ Consultando...       +  spinner en "Generando orientación"
    =100% → ✓ Generando orientación (y redirige)

  El círculo exterior es puramente CSS (rotación + stroke-dashoffset).
=======================================================================
--}}
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Procesando</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
    background: #eef0f8;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
  }

  .screen {
    width: 100%;
    max-width: 390px;
    min-height: 100vh;
    background: linear-gradient(160deg, #f0f2fa 0%, #e8ecf5 40%, #edf0f8 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    overflow: hidden;
  }

  /* Decorative circles top-right */
  .deco {
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    border-radius: 50%;
    border: 28px solid rgba(180,188,220,0.18);
    pointer-events: none;
  }
  .deco2 {
    position: absolute;
    top: 30px; right: -30px;
    width: 140px; height: 140px;
    border-radius: 50%;
    border: 18px solid rgba(180,188,220,0.12);
    pointer-events: none;
  }

  /* ── Animated ring ── */
  .ring-wrap {
    margin-top: 90px;
    position: relative;
    width: 140px; height: 140px;
    display: flex; align-items: center; justify-content: center;
  }

  .ring-svg {
    position: absolute;
    top: 0; left: 0;
    width: 140px; height: 140px;
    animation: ring-rotate 2.4s linear infinite;
    transform-origin: center;
  }

  @keyframes ring-rotate { to { transform: rotate(360deg); } }

  .ring-track {
    fill: none;
    stroke: #d8dcee;
    stroke-width: 8;
  }
  .ring-arc {
    fill: none;
    stroke: #4a7cf7;
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
    stroke: #bcc4e8;
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 24 316;
    stroke-dashoffset: -260;
  }

  .ring-inner {
    width: 76px; height: 76px;
    background: #fff;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(74,124,247,0.18);
    z-index: 1;
  }
  .ring-inner svg { width: 42px; height: 42px; }

  /* ── Card ── */
  .card {
    background: rgba(255,255,255,0.82);
    backdrop-filter: blur(12px);
    border-radius: 28px 28px 0 0;
    margin-top: 36px;
    width: 100%;
    flex: 1;
    padding: 32px 28px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .card-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1d2e;
    text-align: center;
    line-height: 1.35;
    letter-spacing: -0.3px;
    margin-bottom: 10px;
  }
  .card-sub {
    font-size: 14px;
    color: #8a90a8;
    text-align: center;
    margin-bottom: 24px;
  }

  /* ── Progress bar ── */
  .progress-wrap { width: 100%; margin-bottom: 28px; }
  .progress-track {
    width: 100%;
    height: 8px;
    background: #dde0ee;
    border-radius: 8px;
    overflow: hidden;
  }
  .progress-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #4a7cf7, #6a9bff);
    border-radius: 8px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* ── Steps ── */
  .steps { width: 100%; display: flex; flex-direction: column; gap: 18px; }

  .step {
    display: flex;
    align-items: center;
    gap: 12px;
    opacity: 0.3;
    transition: opacity 0.4s ease;
  }
  .step.active { opacity: 1; }
  .step.done   { opacity: 1; }

  .step-icon {
    width: 22px; height: 22px;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
  }

  .spin-svg {
    width: 20px; height: 20px;
    animation: spin 1s linear infinite;
    display: none;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  .check-svg { width: 20px; height: 20px; display: none; }

  .step.active .spin-svg  { display: block; }
  .step.active .check-svg { display: none; }
  .step.done   .spin-svg  { display: none; }
  .step.done   .check-svg { display: block; }

  .step-label {
    font-size: 15px;
    font-weight: 500;
    color: #1a1d2e;
  }

  /* ── Footer ── */
  .footer {
    width: 100%;
    margin-top: auto;
    padding-top: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #9298b0;
    font-size: 13px;
  }
  .footer svg { width: 15px; height: 15px; opacity: 0.7; }
</style>
</head>
<body>
<div class="screen">
  <div class="deco"></div>
  <div class="deco2"></div>

  <!-- Animated ring -->
  <div class="ring-wrap">
    <svg class="ring-svg" viewBox="0 0 140 140" xmlns="http://www.w3.org/2000/svg">
      <circle class="ring-track" cx="70" cy="70" r="62"/>
      <circle class="ring-dash-small" cx="70" cy="70" r="62"/>
      <circle class="ring-arc" cx="70" cy="70" r="62" transform="rotate(-90 70 70)"/>
    </svg>
    <div class="ring-inner">
      <svg viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="18" cy="18" r="12" fill="#e8eeff" stroke="#4a7cf7" stroke-width="2"/>
        <ellipse cx="18" cy="18" rx="6" ry="4" fill="#fff"/>
        <circle cx="18" cy="18" r="2.5" fill="#2a50c8"/>
        <circle cx="19.2" cy="16.8" r="0.9" fill="#fff"/>
        <line x1="27" y1="27" x2="34" y2="34" stroke="#6a3fc8" stroke-width="3" stroke-linecap="round"/>
      </svg>
    </div>
  </div>

  <!-- Card -->
  <div class="card">
    <h1 class="card-title">Respira. Estoy contigo<br>Lo resolveremos paso a paso</h1>
    <p class="card-sub">Esto puede tomar unos segundos...</p>

    <div class="progress-wrap">
      <div class="progress-track">
        <div class="progress-fill" id="progressFill"></div>
      </div>
    </div>

    <div class="steps">

      <div class="step" id="step1">
        <div class="step-icon">
          <svg class="spin-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="8" fill="none" stroke="#dde0ee" stroke-width="2.5"/>
            <path d="M10 2a8 8 0 0 1 8 8" fill="none" stroke="#4a7cf7" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
          <svg class="check-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 10l4 4 8-8" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          </svg>
        </div>
        <span class="step-label">Revisando documento</span>
      </div>

      <div class="step" id="step2">
        <div class="step-icon">
          <svg class="spin-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="8" fill="none" stroke="#dde0ee" stroke-width="2.5"/>
            <path d="M10 2a8 8 0 0 1 8 8" fill="none" stroke="#4a7cf7" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
          <svg class="check-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 10l4 4 8-8" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          </svg>
        </div>
        <span class="step-label">Identificando tipo de procedimiento</span>
      </div>

      <div class="step" id="step3">
        <div class="step-icon">
          <svg class="spin-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="8" fill="none" stroke="#dde0ee" stroke-width="2.5"/>
            <path d="M10 2a8 8 0 0 1 8 8" fill="none" stroke="#4a7cf7" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
          <svg class="check-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 10l4 4 8-8" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          </svg>
        </div>
        <span class="step-label">Consultando fuentes oficiales</span>
      </div>

      <div class="step" id="step4">
        <div class="step-icon">
          <svg class="spin-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <circle cx="10" cy="10" r="8" fill="none" stroke="#dde0ee" stroke-width="2.5"/>
            <path d="M10 2a8 8 0 0 1 8 8" fill="none" stroke="#4a7cf7" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
          <svg class="check-svg" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 10l4 4 8-8" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          </svg>
        </div>
        <span class="step-label">Generando orientación</span>
      </div>

    </div>

    <div class="footer">
      <svg viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="2" y="6" width="11" height="8" rx="2" stroke="#9298b0" stroke-width="1.5"/>
        <path d="M5 6V4a2.5 2.5 0 0 1 5 0v2" stroke="#9298b0" stroke-width="1.5" stroke-linecap="round"/>
        <circle cx="7.5" cy="10" r="1" fill="#9298b0"/>
      </svg>
      No guardamos ningun dato sensible
    </div>
  </div>
</div>

<script>
  // ─── Config ────────────────────────────────────────────────────────
  // $jobId es inyectado por Blade desde el controlador.
  // Cuando JOB_ID = 'demo', corre simulación local (sin backend).
  const JOB_ID   = "{{ $jobId ?? 'demo' }}";
  const POLL_URL = "/emergencia/progreso/" + JOB_ID;

  // Umbrales CSS para activar/completar cada paso
  // activateAt = % para mostrar spinner | doneAt = % para mostrar paloma
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

  // ─── Modo DEMO ──────────────────────────────────────────────────────
  if (JOB_ID === 'demo') {
    let p = 0;
    const t = setInterval(() => {
      p += 2;
      applyProgress(Math.min(p, 100));
      if (p >= 100) clearInterval(t);
    }, 80);

  } else {
    // ─── Modo REAL: polling JSON al endpoint Laravel ────────────────
    // Endpoint debe retornar: { progress: 0-100, done: bool, redirect: string|null }
    const interval = setInterval(async () => {
      try {
        const res  = await fetch(POLL_URL, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        applyProgress(data.progress ?? 0);
        if (data.done) {
          clearInterval(interval);
          setTimeout(() => { window.location.href = data.redirect ?? '/'; }, 700);
        }
      } catch (e) { /* silencioso — reintenta en el siguiente tick */ }
    }, 800);
  }
</script>
</body>
</html>