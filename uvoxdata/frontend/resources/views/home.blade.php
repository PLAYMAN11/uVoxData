@extends('layouts.app')

@section('content')

<style>
.home-screen {
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    padding: 28px 24px;
    background: #F8FAFC;
    overflow: hidden;
}

/* =========================
   BACKGROUND CIRCLES
========================= */

.home-bg {
    position: absolute;
    top: 120px;
    left: 50%;
    transform: translateX(-50%);
    width: 420px;
    height: 420px;
    z-index: 0;
    pointer-events: none;
}

/* base común */
.circle {
    position: absolute;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

/* grande */
.circle-3 {
    width: 420px;
    height: 420px;
    border: 1px solid rgba(95,167,232,0.10);
    box-shadow: 0 30px 80px rgba(0,0,0,0.08);
}

/* medio */
.circle-2 {
    width: 300px;
    height: 300px;
    border: 1px solid rgba(95,167,232,0.18);
    box-shadow: 0 20px 60px rgba(0,0,0,0.06);
}

/* pequeño */
.circle-1 {
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(95,167,232,0.10), transparent 70%);
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
}

/* =========================
   Z LAYERS
========================= */

.home-title-block,
.home-actions,
.home-demo-block {
    position: relative;
    z-index: 2;
}

/* =========================
   TITULO
========================= */

.home-title-block {
    text-align: start;
    margin-bottom: 60px;
}

.home-title {
    font-size: 28px;
    font-weight: 800;
    line-height: 1.2;
    color: #1E293B;
    margin: 0;
}

.home-title-blue {
    color: #5FA7E8;
}

.home-subtitle {
    font-size: 14px;
    color: #475569;
    margin-top: 10px;
}

/* =========================
   BOTONES
========================= */

.home-actions {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: auto;
    margin-bottom: 20px;
}

/* AZUL */
.btn-primary-action {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 16px 18px;

    background: #2F77E2;
    color: white;

    border: none;
    border-radius: 14px;

    font-size: 15px;
    font-weight: 600;

    cursor: pointer;
}

/* ROJO */
.btn-danger-action {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 14px 18px;

    background: white;
    color: #EF4444;

    border: 1.5px solid #EF4444;
    border-radius: 14px;

    font-size: 15px;
    font-weight: 600;

    cursor: pointer;
}

.btn-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-danger-action .btn-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
}

.btn-danger-action small {
    font-size: 12px;
    color: #94A3B8;
}

.btn-arrow {
    display: flex;
    align-items: center;
}

/* =========================
   DEMO
========================= */

.home-demo-block {
    margin-top: auto;
    text-align: center;
    padding-bottom: 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.home-demo-label {
    font-size: 13px;
    color: #94A3B8;
    margin: 0;
}

.home-demo-link {
    font-size: 14px;
    font-weight: 700;
    color: #7C3AED;
    text-decoration: none;

    border-bottom: 1.5px solid #CBD5E1;
    padding-bottom: 2px;
}

.home-demo-link:hover {
    color: #6D28D9;
    border-bottom-color: #6D28D9;
}
</style>

<div class="home-screen">

    <!-- BACKGROUND -->
    <div class="home-bg">
        <div class="circle circle-3"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-1"></div>
    </div>

    <div class="home-title-block">
        <h1 class="home-title">
            ¿Recibiste un<br>
            <span class="home-title-blue">documento oficial?</span>
        </h1>

        <p class="home-subtitle">
            Identifica rápidamente si requiere atención, qué implica y cuánto tiempo tienes para actuar
        </p>
    </div>

    <div class="home-actions">

        <!-- Situación Normal -->
        <button class="btn-primary-action" onclick="window.location.href='{{ route('consulta.documento') }}'">
            <span class="btn-left">
                Revisar documento
            </span>
            <span class="btn-arrow">➜</span>
        </button>

        <!-- Urgencia -->
        <button class="btn-danger-action" onclick="window.location='{{ route('urgencia.documento') }}'">
            <span class="btn-left">
                <span>Orientación urgente</span>
                <small>Necesito actuar rápido</small>
            </span>

            <span class="btn-arrow">➜</span>
        </button>

    </div>

    <div class="home-demo-block">
        <p class="home-demo-label">¿Quieres ver un ejemplo?</p>

        <a href="#" class="home-demo-link">
            Probar demo
        </a>
    </div>

</div>

@endsection