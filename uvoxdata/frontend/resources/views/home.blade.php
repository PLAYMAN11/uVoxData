@extends('layouts.app')

@section('header')
    <x-app-header title="OrientaVox" :showMenu="true" />
@endsection

@section('content')

<div class="home-screen">

    {{-- Decoración --}}
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
            Identifica rápidamente si requiere atención, qué implica y cuánto tiempo tienes para actuar.
        </p>
    </div>

    <div class="home-actions">

        {{-- Revisar documento (flujo normal) --}}
        <a class="btn-primary-action" href="{{ route('consulta.documento') }}">
            <span class="btn-left">
                <span class="btn-title">Revisar documento</span>
                <span class="btn-sub">Subir, escanear o describir</span>
            </span>
            <span class="btn-arrow" aria-hidden="true">›</span>
        </a>

        {{-- Orientación urgente --}}
        <a class="btn-danger-action" href="{{ route('urgencia.documento') }}">
            <span class="btn-left">
                <span class="btn-title">Orientación urgente</span>
                <span class="btn-sub">Necesito actuar rápido</span>
            </span>
            <span class="btn-arrow" aria-hidden="true">›</span>
        </a>

    </div>

    <div class="home-demo-block">
        <p class="home-demo-label">¿Quieres ver un ejemplo?</p>
        <a href="{{ route('demo') }}" class="home-demo-link">Probar demo</a>
    </div>

</div>

@push('styles')
<style>
.app-body { background: #F8FAFC; }

.home-screen {
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    padding: 28px 24px;
    background: #F8FAFC;
    overflow: hidden;
}

/* Decoración de círculos */
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

.circle {
    position: absolute;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.circle-3 {
    width: 420px;
    height: 420px;
    border: 1px solid rgba(95,167,232,0.10);
    box-shadow: 0 30px 80px rgba(0,0,0,0.08);
}

.circle-2 {
    width: 300px;
    height: 300px;
    border: 1px solid rgba(95,167,232,0.18);
    box-shadow: 0 20px 60px rgba(0,0,0,0.06);
}

.circle-1 {
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(95,167,232,0.10), transparent 70%);
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
}

/* Z-index para que el contenido quede sobre la decoración */
.home-title-block,
.home-actions,
.home-demo-block {
    position: relative;
    z-index: 2;
}

/* Título */
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
    letter-spacing: -0.02em;
}

.home-title-blue { color: #5FA7E8; }

.home-subtitle {
    font-size: 14px;
    color: #475569;
    margin-top: 10px;
    line-height: 1.5;
}

/* Botones principales */
.home-actions {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: auto;
    margin-bottom: 20px;
}

.btn-primary-action,
.btn-danger-action {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-radius: 14px;
    text-decoration: none;
    cursor: pointer;
    transition: transform .12s ease, box-shadow .15s;
    -webkit-tap-highlight-color: transparent;
}

.btn-primary-action {
    background: #2F77E2;
    color: #FFFFFF;
    border: none;
    box-shadow: 0 6px 18px rgba(47,119,226,0.25);
}

.btn-danger-action {
    background: #FFFFFF;
    color: #EF4444;
    border: 1.5px solid #EF4444;
}

.btn-primary-action:hover,
.btn-danger-action:hover { transform: translateY(-1px); }

.btn-primary-action:active,
.btn-danger-action:active { transform: scale(0.99); }

.btn-left {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
}

.btn-title {
    font-size: 15px;
    font-weight: 700;
}

.btn-sub {
    font-size: 12px;
    opacity: .85;
}

.btn-primary-action .btn-sub { color: #DCE7FA; }
.btn-danger-action  .btn-sub { color: #94A3B8; }

.btn-arrow {
    font-size: 24px;
    font-weight: 700;
    line-height: 1;
}

/* Demo link */
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
@endpush

@endsection
