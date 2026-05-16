<?php

namespace App\Support;

/**
 * Convierte respuestas del backend FastAPI / RAG al shape `resultado` que consume
 * la vista `consulta/resultado` (sessionStorage.rag_resultado).
 */
class ResultadoMapper
{
    /**
     * @param  array<string, mixed>  $rag  JSON devuelto por POST /documento (respuesta del servicio RAG).
     * @return array<string, mixed>
     */
    public static function fromRagSearch(array $rag): array
    {
        $doc = $rag['document'] ?? [];
        $st = $rag['status'] ?? [];
        $orient = $rag['orientation'] ?? [];

        $prio = $st['priority'] ?? 'informativo';
        $urgencia = match ($prio) {
            'urgente' => 'alta',
            'requiere_atencion' => 'media',
            default => 'baja',
        };

        $acciones = $orient['what_you_can_do_now'] ?? [];
        if (! is_array($acciones)) {
            $acciones = [];
        }

        $headline = $st['headline'] ?? '';
        $remaining = $st['remaining_time'] ?? '';
        $fechaTexto = trim($remaining !== '' && $remaining !== 'No se encontró'
            ? $remaining
            : ($headline !== '' && $headline !== 'No se encontró' ? $headline : ''));

        return [
            'documento_tipo' => $doc['type'] ?? 'Documento',
            'autoridad' => $doc['authority'] ?? '',
            'urgencia' => $urgencia,
            'acciones' => $acciones !== [] ? $acciones : ['Revisa el documento con calma y confirma plazos en la autoridad competente.'],
            'por_que_lo_recibiste' => $orient['why_you_received_this'] ?? '—',
            'consecuencias' => $orient['risk_if_no_action'] ?? '—',
            'fecha_limite_texto' => $fechaTexto,
            'fecha_limite_iso' => '',
        ];
    }

    /**
     * @param  array<string, mixed>  $j  JSON de POST /consulta (ConsultaResponse).
     * @return array<string, mixed>
     */
    public static function fromConsultaIa(array $j): array
    {
        if (! empty($j['necesita_aclaracion'])) {
            $opciones = $j['opciones'] ?? [];
            if (! is_array($opciones)) {
                $opciones = [];
            }

            return [
                'documento_tipo' => 'Aclaración requerida',
                'autoridad' => '',
                'urgencia' => 'media',
                'acciones' => $opciones !== [] ? $opciones : ['Elige la opción que mejor describa tu situación.'],
                'por_que_lo_recibiste' => $j['pregunta_aclaracion'] ?? 'Necesitamos un poco más de contexto para orientarte.',
                'consecuencias' => 'Sin esta aclaración la orientación podría ser incompleta.',
                'fecha_limite_texto' => '',
                'fecha_limite_iso' => '',
                '_necesita_aclaracion' => true,
                '_opciones' => $opciones,
            ];
        }

        $texto = (string) ($j['respuesta'] ?? '');
        $fuentes = $j['fuentes'] ?? [];
        if (! is_array($fuentes)) {
            $fuentes = [];
        }

        $lines = preg_split("/\r\n|\r|\n/", $texto, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $acciones = count($lines) > 1 ? array_slice($lines, 1, 10) : ($texto !== '' ? [$texto] : ['Consulta las fuentes oficiales indicadas.']);

        $modo = $j['modo'] ?? 'degradado';

        return [
            'documento_tipo' => 'Orientación',
            'autoridad' => implode(', ', array_slice(array_filter($fuentes), 0, 3)) ?: 'Fuentes oficiales',
            'urgencia' => $modo === 'full' ? 'media' : 'sin_plazo',
            'acciones' => $acciones,
            'por_que_lo_recibiste' => $lines[0] ?? $texto ?: '—',
            'consecuencias' => $fuentes !== [] ? ('Fuentes: '.implode('; ', $fuentes)) : '—',
            'fecha_limite_texto' => '',
            'fecha_limite_iso' => '',
        ];
    }
}
