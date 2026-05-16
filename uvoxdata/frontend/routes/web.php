<?php

use Illuminate\Support\Facades\Route;

// ── Home ──────────────────────────────────────────────
Route::get('/', function () {
    return view('home');
})->name('home');

// ── Flujo Normal ──────────────────────────────────────
Route::get('/consulta/documento',   fn() => view('consulta.documento'))->name('consulta.documento');
Route::get('/consulta/descripcion', fn() => view('consulta.descripcion'))->name('consulta.descripcion');
Route::get('/consulta/procesando',  fn() => view('consulta.procesando'))->name('consulta.procesando');
Route::get('/consulta/resultado',   fn() => view('consulta.resultado'))->name('consulta.resultado');

// ── Flujo Urgencia ────────────────────────────────────
Route::get('/urgencia',             fn() => view('urgencia.index'))->name('urgencia.index');
Route::get('/urgencia/documento',   fn() => view('urgencia.documento'))->name('urgencia.documento');
Route::get('/urgencia/descripcion', fn() => view('urgencia.descripcion'))->name('urgencia.descripcion');
Route::get('/urgencia/procesando',  fn() => view('urgencia.procesando'))->name('urgencia.procesando');
Route::get('/urgencia/resultado',   fn() => view('urgencia.resultado'))->name('urgencia.resultado');

// ── Demo ──────────────────────────────────────────────
Route::get('/demo', fn() => view('demo'))->name('demo');