<?php

namespace App\Services\LarvasApi;

/**
 * Cliente de API_larvas. Implementación: HttpLarvasApiClient (en los tests se usa con Http::fake()).
 * Ante cualquier falla lanza LarvasApiException con un error_code estable: api_unreachable,
 * api_timeout_possibly_completed, un código por estado HTTP (LarvasApiException::STATUS_CODES),
 * hash_mismatch o contract_mismatch. Los mensajes para el usuario están en LarvasApiException::MESSAGES.
 */
interface LarvasApiClient
{
    /** GET /health → arreglo decodificado. */
    public function health(): array;

    /** GET /api/v1/model-info → arreglo decodificado. */
    public function modelInfo(): array;

    /**
     * POST /api/v1/predict con include_polygons=true y return_image=false.
     *
     * @param  string      $path          ruta absoluta del archivo de imagen
     * @param  string      $filename      nombre original (solo informativo)
     * @param  string      $localSha256   SHA-256 calculado por SIDEc antes de enviar
     * @param  float|null  $umPerPx       µm por píxel (solo si se piden medidas en µm)
     */
    public function predict(string $path, string $filename, string $localSha256, ?float $umPerPx = null): PredictResult;
}
