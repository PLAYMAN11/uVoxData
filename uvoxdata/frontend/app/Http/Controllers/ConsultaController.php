<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConsultaController extends Controller
{
    public function index()
    {
        return view('consulta.index');
    }

    public function consultar(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string|max:2000',
        ]);

        $respuesta = Http::timeout(30)->post(config('services.backend.url') . '/consulta', [
            'pregunta' => $request->input('pregunta'),
            'contexto' => $request->input('contexto'),
        ]);

        if ($respuesta->failed()) {
            return response()->json(['error' => 'El servicio no está disponible.'], 503);
        }

        return response()->json($respuesta->json());
    }

    public function subirDocumento(Request $request)
    {
        $request->validate([
            'documento' => 'required|file|mimes:pdf|max:10240',
        ]);

        $respuesta = Http::timeout(60)->attach(
            'archivo',
            file_get_contents($request->file('documento')->getRealPath()),
            $request->file('documento')->getClientOriginalName()
        )->post(config('services.backend.url') . '/documento');

        if ($respuesta->failed()) {
            return response()->json(['error' => 'Error al procesar el documento.'], 503);
        }

        return response()->json($respuesta->json());
    }
}
