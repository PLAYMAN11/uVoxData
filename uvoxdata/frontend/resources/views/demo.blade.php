@extends('layouts.app')

@section('shellClass', 'light-shell')

@section('header')
    <x-app-header :back="route('home')" title="Demo consulta" />
@endsection

@section('content')
<div class="doc-wrap" style="padding: 0 4px;">
    <p style="font-size:14px;color:#64748B;margin-bottom:16px;">
        Prueba <code style="font-size:12px;">POST /consulta</code> vía Laravel → FastAPI → RAG. Requiere <code style="font-size:12px;">BACKEND_URL</code> y servicio RAG en marcha.
    </p>

    <div id="aclaracion-container" class="hidden">
        <p id="clarification-question" style="font-weight:600;margin-bottom:8px;"></p>
        <div id="clarification-options" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
    </div>

    <div id="respuesta-container" class="hidden">
        <p id="response-mode-badge" style="display:inline-block;margin-bottom:8px;"></p>
        <div id="response-text" style="white-space:pre-wrap;font-size:14px;line-height:1.55;margin-bottom:12px;"></div>
        <div id="response-sources" style="font-size:13px;color:#475569;"></div>
    </div>

    <form id="consulta-form" style="margin-top:20px;">
        <label for="pregunta" style="display:block;font-weight:600;margin-bottom:6px;">Pregunta</label>
        <textarea id="pregunta" rows="5" maxlength="2000" style="width:100%;box-sizing:border-box;padding:10px;border-radius:10px;border:1px solid #CBD5E1;font-family:inherit;"></textarea>
        <p id="char-count" style="font-size:12px;color:#64748B;margin:6px 0 10px;">0 / 2000</p>
        <button type="submit" style="background:#2563EB;color:#fff;border:none;border-radius:10px;padding:10px 20px;font-weight:600;cursor:pointer;">Enviar</button>
    </form>
</div>

@push('styles')
<style>
.hidden { display: none !important; }
</style>
@endpush
@endsection
