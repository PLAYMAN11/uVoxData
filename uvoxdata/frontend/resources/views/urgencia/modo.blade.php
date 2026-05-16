<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Emergencia</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
    background: #f2f2f7;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .phone-wrapper {
    width: 100%;
    max-width: 390px;
    min-height: 100vh;
    background: #f2f2f7;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
  }

/* HEADER REUSABLE */
.lh-header {
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    background: #F2F2F7;
    position: relative;
}

.lh-back {
    color: #EF4444;
    text-decoration: none;
}

.lh-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 17px;
    font-weight: 700;
    color: #EF4444;
}

.lh-menu-btn {
    background: #FFFFFF;
    border: 1.5px solid #D1D9E6;
    border-radius: 10px;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
  

  /* Progress */
  .progress-bar {
    padding: 0 20px 20px;
  }

  .progress-label {
    font-size: 12px;
    color: #8e8e93;
    margin-bottom: 6px;
    font-weight: 400;
  }

  .progress-track {
    height: 4px;
    background: #d1d1d6;
    border-radius: 4px;
    overflow: hidden;
  }

  .progress-fill {
    height: 100%;
    width: 33.33%;
    background: #e63946;
    border-radius: 4px;
    transition: width 0.4s ease;
  }

  /* Hero */
  .hero {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px 20px 20px;
  }

  .hero-icon {
    width: 88px; height: 88px;
    background: #e63946;
    border-radius: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 28px;
    box-shadow: 0 8px 28px rgba(230, 57, 70, 0.35);
  }

  .hero-icon svg { width: 44px; height: 44px; fill: #fff; }

  .hero-title {
    font-size: 22px;
    font-weight: 700;
    color: #1c1c1e;
    text-align: center;
    line-height: 1.25;
    letter-spacing: -0.4px;
    margin-bottom: 10px;
  }

  .hero-subtitle {
    font-size: 15px;
    color: #636366;
    text-align: center;
    line-height: 1.5;
    max-width: 280px;
  }

  /* Options */
  .options {
    padding: 12px 20px 32px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .option-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    text-decoration: none;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    transition: transform 0.12s ease, box-shadow 0.12s ease;
    -webkit-tap-highlight-color: transparent;
  }

  .option-card:active {
    transform: scale(0.98);
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
  }

  .option-icon-wrap {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .option-icon-wrap svg { width: 24px; height: 24px; }

  .icon-red    { background: #fff1f1; }
  .icon-orange { background: #fff4ec; }
  .icon-purple { background: #f2f0ff; }

  .option-text { flex: 1; }

  .option-title {
    font-size: 15px;
    font-weight: 600;
    color: #1c1c1e;
    letter-spacing: -0.2px;
    margin-bottom: 3px;
  }

  .option-desc {
    font-size: 13px;
    color: #8e8e93;
    line-height: 1.4;
  }

  .option-chevron {
    color: #c7c7cc;
    flex-shrink: 0;
  }

  .option-chevron svg { width: 9px; height: 15px; }
</style>
</head>
<body>
<div class="phone-wrapper">

  {{-- Header --}}
  <div class="lh-header">

    <a href="{{ route('home') }}" class="lh-back d-flex align-items-center">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
            <path d="M13 5l-6 6 6 6" stroke="#1E293B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <span class="lh-title">Situación Urgente</span>

    <button class="lh-menu-btn" aria-label="Menú">
        <svg width="18" height="14" viewBox="0 0 18 14" fill="none">
            <path d="M1 1h16M1 7h16M1 13h16" stroke="#1E293B" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>

  </div>


  {{-- Progress --}}
  <div class="progress-bar">
    <div class="progress-label">Paso 1 de 3</div>
    <div class="progress-track">
      <div class="progress-fill"></div>
    </div>
  </div>

  {{-- Hero --}}
  <div class="hero">
    <div class="hero-icon">
      {{-- Alarm clock icon --}}
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2a9 9 0 1 0 0 18A9 9 0 0 0 12 2zm0 2a7 7 0 1 1 0 14A7 7 0 0 1 12 4zm0 2v6l4 2-1 1.7L11 14V6h1zM5 2.18L1.5 5.6l1.41 1.42L6.42 3.6 5 2.18zM19 2.18l-1.42 1.42 3.51 3.42L22.5 5.6 19 2.18z"/>
      </svg>
    </div>
    <h1 class="hero-title">¿Qué describe mejor<br>tu situación?</h1>
    <p class="hero-subtitle">Selecciona la opción que mejor describa tu situación para poder orientarte de inmediato.</p>
  </div>

  {{-- Option cards --}}
  <div class="options">

    <a href="#" class="option-card" onclick="setEmergencia('urgente')">
      <div class="option-icon-wrap icon-red">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 2L2 20h20L12 2z" fill="#e63946" opacity=".15"/>
          <path d="M12 2L2 20h20L12 2z" stroke="#e63946" stroke-width="1.8" stroke-linejoin="round"/>
          <path d="M12 9v5" stroke="#e63946" stroke-width="2" stroke-linecap="round"/>
          <circle cx="12" cy="16.5" r="1" fill="#e63946"/>
        </svg>
      </div>
      <div class="option-text">
        <div class="option-title">Me notificaron algo urgente</div>
        <div class="option-desc">Recibí un documento oficial con plazo corto de tiempo</div>
      </div>
      <div class="option-chevron">
        <svg viewBox="0 0 9 15" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1l7 6.5L1 14" stroke="#c7c7cc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </a>

    <a href="#" class="option-card" onclick="setEmergencia('violentado')">
      <div class="option-icon-wrap icon-orange">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="9" fill="#ff6b2b" opacity=".15"/>
          <circle cx="12" cy="12" r="9" stroke="#ff6b2b" stroke-width="1.8"/>
          <path d="M8.5 9.5C8.5 9.5 9 8 12 8c3 0 3.5 2 2.5 3.5C13 13 12 13.5 12 15" stroke="#ff6b2b" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="12" cy="17.5" r="1" fill="#ff6b2b"/>
        </svg>
      </div>
      <div class="option-text">
        <div class="option-title">Me violentaron</div>
        <div class="option-desc">Necesito asesoría de como responder</div>
      </div>
      <div class="option-chevron">
        <svg viewBox="0 0 9 15" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1l7 6.5L1 14" stroke="#c7c7cc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </a>

    <a href="#" class="option-card" onclick="setEmergencia('no-se')">
      <div class="option-icon-wrap icon-purple">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="9" fill="#7c5cbf" opacity=".15"/>
          <circle cx="12" cy="12" r="9" stroke="#7c5cbf" stroke-width="1.8"/>
          <path d="M9 9.5C9 8 10.5 7 12 7c1.7 0 3 1.1 3 2.5 0 1.8-2 2.5-2 4" stroke="#7c5cbf" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="12" cy="16.5" r="1" fill="#7c5cbf"/>
        </svg>
      </div>
      <div class="option-text">
        <div class="option-title">No entiendo que hacer</div>
        <div class="option-desc">Me llegó un documento oficial y no se que hacer</div>
      </div>
      <div class="option-chevron">
        <svg viewBox="0 0 9 15" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1l7 6.5L1 14" stroke="#c7c7cc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </a>

  </div>

</div>
<script>
// Guardar selección en sessionStorage antes de navegar
document.querySelectorAll('.option-card').forEach(card => {
    card.addEventListener('click', function () {
        const title = this.querySelector('.option-title')?.innerText || '';
        sessionStorage.setItem('modo_seleccionado', title);
    });
});

// backend state control
function setEmergencia(tipo) {
    fetch("{{ route('emergencia.set') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ tipo })
    })
    .then(() => {
        window.location.href = "{{ route('consulta.documento') }}";
    });
}
</script>
</body>
</html>