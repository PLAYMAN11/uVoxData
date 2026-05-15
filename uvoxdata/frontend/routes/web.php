<?php

use App\Http\Controllers\ConsultaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ConsultaController::class, 'index'])->name('consulta.index');
Route::post('/consulta', [ConsultaController::class, 'consultar'])->name('consulta.consultar');
Route::post('/documento', [ConsultaController::class, 'subirDocumento'])->name('consulta.documento');
