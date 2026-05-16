@extends('layouts.app')

@section('content')

<style>
/* ============================================================
   URGENCIA — INDEX
   ============================================================ */

.urg-screen {
  position: relative;
  display: flex;
  flex-direction: column;
  min-height: 100%;
  background: var(--bg);
  overflow: hidden;
}

/* ---- Fondo marca de agua (arcos rojos difuminados) ---- */
.urg-bg {
  position: absolute;
  top: -60px;
  right: -80px;
  width: 320px;
  height: 320px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(239,68,68,0.07) 0%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

.urg-bg-2 {
  position: absolute;
  bottom: 40px;
  left: -100px;
  width: 260px;
  height: 260px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(239,68,68,0.04) 0%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

/* ---- Sub-header: back + título + menú ---- */
.urg-subheader {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 18px 8px;
  position: relative;
  z-index: 2;
}

.urg-back {
  width: 34px;
  height: 34px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-inverse);
  text-decoration: none;
  flex-shrink: 0;
}

.urg-back:hover {
  color: var(--danger);
}

.urg-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--danger);
  margin: 0;
  text-align: center;
  flex: 1;
}

.urg-menu {
  width: 34px;
  height: 34px;
  border-radius: 9px;
  border: 1.5px solid var(--border);
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: var(--text-secondary);
  flex-shrink: 0;
}

/* ---- Progress bar ---- */
.urg-progress-block {
  padding: 4px 20px 16px;
  position: relative;
  z-index: 2;
}

.urg-progress-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin: 0 0 6px;
}

.urg-progress-bar {
  width: 100%;
  height: 5px;
  background: var(--border);
  border-radius: 99px;
  overflow: hidden;
}

.urg-progress-fill {
  height: 100%;
  width: 33.33%;
  background: var(--primary-dark);
  border-radius: 99px;
  transition: width 0.4s ease;
}

/* ---- Ícono central ---- */
.urg-icon-wrap {
  display: flex;
  justify-content: center;
  padding: 8px 0 20px;
  position: relative;
  z-index: 2;
}

.urg-icon-box {
  width: 88px;
  height: 88px;
  border-radius: 26px;
  background: var(--danger);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 28px rgba(239,68,68,0.35);
}

/* ---- Pregunta central ---- */
.urg-question-block {
  text-align: center;
  padding: 0 24px 20px;
  position: relative;
  z-index: 2;
}

.urg-question {
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--text-inverse);
  margin: 0 0 10px;
  line-height: 1.3;
  letter-spacing: -0.3px;
}

.urg-question-sub {
  font-size: 0.85rem;
  color: var(--text-secondary);
  margin: 0;
  line-height: 1.55;
}

/* ---- Cards de opciones ---- */
.urg-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 4px 20px 28px;
  position: relative;
  z-index: 2;
}

.urg-option {
  display: flex;
  align-items: center;
  gap: 14px;
  background: white;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-card);
  padding: 16px 14px 16px 16px;
  text-decoration: none;
  color: var(--text-inverse);
  transition: border-color 0.16s, box-shadow 0.16s, transform 0.12s;
}

.urg-option:hover {
  border-color: var(--primary);
  box-shadow: 0 2px 12px rgba(95,167,232,0.15);
  transform: translateY(-1px);
}

.urg-option:active {
  transform: scale(0.98);
}

.urg-option-icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.urg-option-icon.red    { background: var(--danger); }
.urg-option-icon.orange { background: #F97316; }
.urg-option-icon.purple { background: var(--secondary); }

.urg-option-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.urg-option-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--text-inverse);
  line-height: 1.2;
}

.urg-option-desc {
  font-size: 0.78rem;
  color: var(--text-secondary);
  line-height: 1.4;
}

.urg-option-arrow {
  color: var(--text-secondary);
  flex-shrink: 0;
}
</style>

<div class="urg-screen">

    {{-- Fondos difuminados --}}
    <div class="urg-bg" aria-hidden="true"></div>
    <div class="urg-bg-2" aria-hidden="true"></div>

    {{-- Sub-header --}}
    <div class="urg-subheader">
        <a href="{{ route('home') }}" class="urg-back" aria-label="Volver">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M13 4l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
        <h2 class="urg-title">Emergencia</h2>
        <button class="urg-menu" aria-label="Menú">
            <svg width="16" height="12" viewBox="0 0 16 12" fill="none">
                <line x1="0" y1="1"  x2="16" y2="1"  stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                <line x1="0" y1="6"  x2="16" y2="6"  stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                <line x1="0" y1="11" x2="16" y2="11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    {{-- Progress --}}
    <div class="urg-progress-block">
        <p class="urg-progress-label">Paso 1 de 3</p>
        <div class="urg-progress-bar">
            <div class="urg-progress-fill"></div>
        </div>
    </div>

    {{-- Ícono alarma --}}
    <div class="urg-icon-wrap">
        <div class="urg-icon-box">
            <svg width="46" height="46" viewBox="0 0 46 46" fill="none">
                <!-- Campana / reloj despertador -->
                <circle cx="23" cy="25" r="14" fill="white" opacity="0.95"/>
                <circle cx="23" cy="25" r="10" fill="var(--danger)"/>
                <!-- Manecillas -->
                <line x1="23" y1="25" x2="23" y2="19" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                <line x1="23" y1="25" x2="27" y2="27" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                <!-- Patas -->
                <line x1="15" y1="37" x2="12" y2="40" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <line x1="31" y1="37" x2="34" y2="40" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <!-- Campanita izq -->
                <circle cx="10" cy="17" r="4" fill="white" opacity="0.9"/>
                <!-- Campanita der -->
                <circle cx="36" cy="17" r="4" fill="white" opacity="0.9"/>
                <!-- Palito -->
                <line x1="23" y1="11" x2="23" y2="14" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    {{-- Pregunta --}}
    <div class="urg-question-block">
        <h3 class="urg-question">¿Qué describe mejor tu situación?</h3>
        <p class="urg-question-sub">Selecciona la opción que mejor describa tu<br>situación para poder orientarte de inmediato.</p>
    </div>

    {{-- Opciones --}}
    <div class="urg-options">

        <a href="{{ route('urgencia.documento') }}" class="urg-option">
            <span class="urg-option-icon red">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M12 4L4 20h16L12 4z" stroke="white" stroke-width="2" stroke-linejoin="round"/>
                    <line x1="12" y1="10" x2="12" y2="15" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="17.5" r="1" fill="white"/>
                </svg>
            </span>
            <span class="urg-option-text">
                <span class="urg-option-title">Me notificaron algo urgente</span>
                <span class="urg-option-desc">Recibí un documento oficial con plazo corto de tiempo</span>
            </span>
            <span class="urg-option-arrow">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </a>

        <a href="{{ route('urgencia.documento') }}" class="urg-option">
            <span class="urg-option-icon orange">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="white" stroke-width="1.8"/>
                    <path d="M9 9.5C9 8.1 10.3 7 12 7s3 1.1 3 2.5c0 1.8-2 2.5-2 4" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="12" cy="16.5" r="1" fill="white"/>
                </svg>
            </span>
            <span class="urg-option-text">
                <span class="urg-option-title">Me violentaron</span>
                <span class="urg-option-desc">Necesito asesoría de como responder</span>
            </span>
            <span class="urg-option-arrow">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </a>

        <a href="{{ route('urgencia.documento') }}" class="urg-option">
            <span class="urg-option-icon purple">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="white" stroke-width="1.8"/>
                    <path d="M9 9.5C9 8.1 10.3 7 12 7s3 1.1 3 2.5c0 1.8-2 2.5-2 4" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="12" cy="16.5" r="1" fill="white"/>
                </svg>
            </span>
            <span class="urg-option-text">
                <span class="urg-option-title">No entiendo que hacer</span>
                <span class="urg-option-desc">Me llegó un documento oficial y no se que hacer</span>
            </span>
            <span class="urg-option-arrow">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </a>

    </div>

</div>

@endsection