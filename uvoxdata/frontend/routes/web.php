<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultaController;

// ── Home ──────────────────────────────────────────────
Route::get('/', function () {
    return view('home');
})->name('home');

// ── Flujo Normal ──────────────────────────────────────
Route::get('/consulta/documento',   fn() => view('consulta.documento'))->name('consulta.documento');
Route::post('/consulta/subir',      [ConsultaController::class, 'subirDocumento'])->name('consulta.subir');
Route::get('/consulta/descripcion', fn() => view('consulta.descripcion'))->name('consulta.descripcion');
Route::post('/consulta/describir',  [ConsultaController::class, 'consultar'])->name('consulta.describir');
Route::get('/consulta/procesando',  fn() => view('consulta.procesando'))->name('consulta.procesando');
Route::get('/consulta/resultado',   fn() => view('consulta.resultado'))->name('consulta.resultado');

// ── Flujo Urgencia ────────────────────────────────────
Route::get('/urgencia/documento', function () {
    return view('urgencia.emergencia-desc');
})->name('urgencia.documento');

// ── Demo ──────────────────────────────────────────────
Route::get('/demo', fn() => view('demo'))->name('demo');
