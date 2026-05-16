@extends('layouts.app')

@section('content')

<div class="home-screen d-flex flex-column align-items-center justify-content-between h-100 px-4 py-5">

    {{-- Top Section --}}
    <div class="text-center mt-3">
        <p class="home-eyebrow mb-2">Identifica rápidamente el trámite adecuado, qué implica y cuánto tiempo tienes para actuar.</p>
    </div>

    {{-- Central Icon --}}
    <div class="home-icon-wrapper my-4">
        <div class="home-icon-bg">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Document -->
                <rect x="10" y="6" width="26" height="34" rx="4" fill="var(--primary-soft)" stroke="var(--primary)" stroke-width="2"/>
                <line x1="16" y1="16" x2="30" y2="16" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"/>
                <line x1="16" y1="22" x2="30" y2="22" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"/>
                <line x1="16" y1="28" x2="24" y2="28" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"/>
                <!-- Magnifier -->
                <circle cx="38" cy="38" r="10" fill="white" stroke="var(--primary-dark)" stroke-width="2.5"/>
                <line x1="45" y1="45" x2="51" y2="51" stroke="var(--primary-dark)" stroke-width="2.5" stroke-linecap="round"/>
                <!-- Search dot -->
                <circle cx="38" cy="38" r="4" fill="var(--primary-soft)"/>
            </svg>
        </div>
    </div>

    {{-- Main Title --}}
    <div class="text-center px-2 mb-4">
        <h1 class="home-title mb-2">¿Recibiste un<br><span class="home-title-highlight">documento oficial?</span></h1>
        <p class="home-subtitle">Identifica rápidamente el trámite adecuado, qué implica y cuánto tiempo tienes para actuar.</p>
    </div>

    {{-- CTA Buttons --}}
    <div class="w-100 d-flex flex-column gap-3 mb-3" style="max-width: var(--w-regular);">

        <a href="#" class="btn btn-primary btn-home w-100 d-flex align-items-center justify-content-between px-4">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M4 4h8l4 4v8H4V4z" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M12 4v4h4" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                </svg>
                <span>Revisar documento</span>
            </div>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M6 4l4 4-4 4" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

        <a href="#" class="btn btn-home-danger w-100 d-flex align-items-center justify-content-between px-4">
            <div class="d-flex align-items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <circle cx="10" cy="10" r="8" stroke="var(--danger)" stroke-width="1.5"/>
                    <path d="M10 6v5" stroke="var(--danger)" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="10" cy="14" r="1" fill="var(--danger)"/>
                </svg>
                <span>Urgencia Urgente</span>
            </div>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M6 4l4 4-4 4" stroke="var(--danger)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

    </div>

    {{-- Example Link --}}
    <a href="#" class="home-example-link mb-4">¿Sabes un ejemplo?</a>

    {{-- Footer Note --}}
    <p class="home-privacy">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="margin-right:4px;">
            <path d="M6 1L2 3v3c0 2.5 1.8 4.7 4 5.5C8.2 10.7 10 8.5 10 6V3L6 1z" stroke="var(--text-secondary)" stroke-width="1" stroke-linejoin="round"/>
        </svg>
        Información cifrada y sin guardar datos sensibles
    </p>

</div>

@endsection