<?php

namespace App\Http\Controllers;

use App\Support\ResultadoMapper;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class ConsultaController extends Controller
{
    public function index()
    {
        return view('consulta.index');
    }

    /**
     * POST /consulta — JSON crudo hacia FastAPI (p. ej. demo / api.js).
     */
    public function consultar(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string|max:2000',
            'contexto' => 'nullable|string|max:8000',
        ]);

        $respuesta = Http::timeout(120)->post(config('services.backend.url').'/consulta', [
            'pregunta' => $request->input('pregunta'),
            'contexto' => $request->input('contexto'),
        ]);

        if ($respuesta->failed()) {
            return response()->json(['error' => 'El servicio no está disponible.'], 503);
        }

        return response()->json($respuesta->json());
    }

    /**
     * POST /consulta/describir — wizard “sin documento”; devuelve { ok, resultado } para sessionStorage.
     */
    public function describirOrientacion(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string|max:2000',
            'contexto' => 'nullable|string|max:8000',
        ]);

        $respuesta = Http::timeout(120)->post(config('services.backend.url').'/consulta', [
            'pregunta' => $request->input('pregunta'),
            'contexto' => $request->input('contexto'),
        ]);

        if ($respuesta->failed()) {
            return response()->json(['error' => 'El servicio no está disponible.'], 503);
        }

        $payload = $respuesta->json();
        $resultado = ResultadoMapper::fromConsultaIa(is_array($payload) ? $payload : []);

        return response()->json([
            'ok' => true,
            'resultado' => $resultado,
        ]);
    }

    /**
     * POST /documento — respuesta cruda del backend (RAG /search vía FastAPI).
     */
    public function subirDocumento(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ]);

        $json = $this->proxyDocumentoAnalysis($request->file('archivo'));
        if ($json === null) {
            return response()->json(['error' => 'Error al procesar el documento.'], 503);
        }

        return response()->json($json);
    }

    /**
     * POST /consulta/subir — wizard con documento; devuelve { ok, mensaje, tipo, resultado }.
     */
    public function subirOrientacionDocumento(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ]);

        $file = $request->file('archivo');
        $json = $this->proxyDocumentoAnalysis($file);
        if ($json === null) {
            return response()->json(['error' => 'Error al procesar el documento.'], 503);
        }

        $resultado = ResultadoMapper::fromRagSearch($json);

        return response()->json([
            'ok' => true,
            'mensaje' => 'archivo recibido',
            'tipo' => $file->getClientOriginalExtension(),
            'resultado' => $resultado,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function proxyDocumentoAnalysis(UploadedFile $file): ?array
    {
        $respuesta = Http::timeout(90)->attach(
            'archivo',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName(),
            ['Content-Type' => $file->getMimeType()]
        )->post(config('services.backend.url').'/documento');

        if ($respuesta->failed()) {
            return null;
        }

        $json = $respuesta->json();

        return is_array($json) ? $json : null;
    }
}
