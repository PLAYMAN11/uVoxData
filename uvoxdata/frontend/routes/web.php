<?php

use Illuminate\Support\Facades\Route;

// ── Home ──────────────────────────────────────────────
Route::get('/', function () {
    return view('home');
})->name('home');

// ── Flujo Normal ──────────────────────────────────────


// ── Flujo Urgencia ────────────────────────────────────
Route::get('/urgencia/documento', function () {
    return view('urgencia.emergencia-desc');
})->name('urgencia.documento');

// ── Demo ──────────────────────────────────────────────
Route::get('/demo', fn() => view('demo'))->name('demo');