<?php

use App\Http\Controllers\ConsultaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/* ───────────────── HOME ────────────────────── */
Route::get('/', function () {
    return view('home');
})->name('home');


/* ────────────── CONSULTA — FLUJO DOCUMENTO ──────────── */

// Paso 2 — ¿tienes el documento?
Route::get('/consulta/documento', function () {
    return view('consulta.documento');
})->name('consulta.documento');

// Subir documento — wizard (mapea respuesta RAG al formato resultado)
Route::post('/consulta/subir', [ConsultaController::class, 'subirOrientacionDocumento'])->name('consulta.subir');

// Proxy Laravel → FastAPI (api.js / integraciones)
Route::post('/consulta', [ConsultaController::class, 'consultar'])->name('consulta.ia');
Route::post('/documento', [ConsultaController::class, 'subirDocumento'])->name('documento.ia');

// Paso 3 — describir situación sin documento
Route::get('/consulta/descripcion', function () {
    return view('consulta.descripcion');
})->name('consulta.descripcion');

// Describir — wizard (mapea ConsultaResponse al formato resultado)
Route::post('/consulta/describir', [ConsultaController::class, 'describirOrientacion'])->name('consulta.describir');

// Pantalla intermedia de procesamiento (solo vista)
Route::view('/consulta/procesando', 'procesando')->name('consulta.procesando');

// Resultado del análisis
Route::view('/consulta/resultado', 'consulta.resultado')->name('consulta.resultado');

// Orientación por temas + chat conversacional (POST /consulta vía api.js)
Route::view('/consulta/chat', 'chat')->name('consulta.chat');


/* ────────────────── URGENCIA ──────────────────── */

// Paso 1 — ¿qué describe mejor tu situación?
Route::get('/urgencia/documento', function () {
    return view('urgencia.modo');
})->name('urgencia.documento');

// Guarda el modo en sesión (consumido por el wizard)
Route::post('/emergencia/set', function (Request $request) {
    session(['emergencia_tipo' => $request->tipo]);
    return response()->json(['ok' => true]);
})->name('emergencia.set');


/* ─────────────────── DEMO ─────────────────── */
Route::get('/demo', fn () => view('demo'))->name('demo');
