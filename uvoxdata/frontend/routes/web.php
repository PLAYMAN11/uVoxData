<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/* ───────────────── HOME ────────────────────── */
Route::get('/', function () {
    return view('home');
})->name('home');


/* ────────────── CONSULTA FLUJO ──────────────────────── */

// Pantalla donde subes documento
Route::get('/consulta/documento', function () {
    return view('consulta.documento');
})->name('consulta.documento');


// Endpoint upload (fetch)
Route::post('/consulta/subir', function (Request $request) {

    return response()->json([
        'ok' => true,
        'mensaje' => 'archivo recibido',
        'tipo' => $request->file('archivo')?->getClientOriginalExtension()
    ]);

})->name('consulta.subir');


// Pantalla donde escribes descripción
Route::get('/consulta/descripcion', function () {
    return view('consulta.descripcion');
})->name('consulta.descripcion');


// API: procesar descripción
Route::post('/consulta/describir', function (Request $request) {

    return response()->json([
        'ok' => true,
        'resultado' => [
            'documento_tipo' => 'Demo',
            'autoridad' => 'Sistema',
            'urgencia' => 'media',
            'acciones' => [
                'Revisar documento',
                'Acudir a asesoría',
                'Responder antes del plazo'
            ],
            'por_que_lo_recibiste' => 'Caso simulado para prueba del flujo',
            'consecuencias' => 'Puede generar sanciones si no respondes',
            'fecha_limite_texto' => '30 de mayo',
            'fecha_limite_iso' => '2026-05-30'
        ]
    ]);

})->name('consulta.describir');


// Resultado
Route::get('/consulta/resultado', function () {
    return view('resultado');
})->name('consulta.resultado');

/* ────────URGENCIA ─────────────────── */

Route::get('/urgencia/documento', function () {
    return view('urgencia.modo');
})->name('urgencia.documento');

Route::get('/emergencia/urgente', function () {
    return view('consulta.documento');
})->name('emergencia.urgente');

Route::get('/emergencia/violentaron', function () {
    return view('consulta.documento');
})->name('emergencia.violentaron');

Route::get('/emergencia/no-se', function () {
    return view('consulta.documento');
})->name('emergencia.no-se');

Route::post('/emergencia/set', function (\Illuminate\Http\Request $request) {
    session(['emergencia_tipo' => $request->tipo]);
    return response()->json(['ok' => true]);
})->name('emergencia.set');

Route::get('/urgencia/emergencia-doc', function () {
    return view('urgencia.emergencia-doc');
})->name('urgencia.emergencia-doc');
/* ─────────────────────────────
 | DEMO
───────────────────────────── */
Route::get('/demo', fn () => view('demo'))->name('demo');