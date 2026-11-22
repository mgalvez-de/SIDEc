<?php

namespace App\Services\LarvasApi;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente real de API_larvas (servidor a servidor, header X-API-Key).
 */
class HttpLarvasApiClient implements LarvasApiClient
{
    /** @var array config('larvas_api') */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function health(): array
    {
        return $this->getJson('/health');
    }

    public function modelInfo(): array
    {
        return $this->getJson('/api/v1/model-info');
    }

    public function predict(string $path, string $filename, string $localSha256, ?float $umPerPx = null): PredictResult
    {
        $query = ['include_polygons' => 'true', 'return_image' => 'false'];
        if ($umPerPx !== null) {
            $query['um_per_px'] = $umPerPx;
        }

        $start = microtime(true);
        $handle = fopen($path, 'r');

        try {
            $response = $this->request()
                ->attach('file', $handle, $filename)
                ->post('/api/v1/predict?' . http_build_query($query));
        } catch (ConnectionException $e) {
            throw $this->connectionError($e, $start, $localSha256);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        $elapsed = $this->elapsedMs($start);

        if ($response->status() !== 200) {
            $exception = LarvasApiException::fromStatus($response->status(), $response->body(), $elapsed);
            $this->log($exception, $localSha256);
            throw $exception;
        }

        try {
            return PredictResult::fromBody($response->body(), $localSha256, $elapsed, 200);
        } catch (LarvasApiException $e) {
            $this->log($e, $localSha256);
            throw $e;
        }
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl($this->config['url'])
            ->withHeaders(array_filter([
                'X-API-Key' => $this->config['key'] ?: null,
                'Accept'    => 'application/json',
            ]))
            ->timeout(max(1, (int) $this->config['timeout']))
            ->withOptions(['connect_timeout' => max(1, (int) $this->config['connect_timeout'])]);
    }

    private function getJson(string $uri): array
    {
        $start = microtime(true);

        try {
            $response = $this->request()->get($uri);
        } catch (ConnectionException $e) {
            throw $this->connectionError($e, $start, null);
        }

        if ($response->status() !== 200) {
            throw LarvasApiException::fromStatus($response->status(), $response->body(), $this->elapsedMs($start));
        }

        $json = $response->json();
        if (! is_array($json)) {
            throw new LarvasApiException('contract_mismatch', 'La respuesta no es JSON.', 200, $response->body());
        }

        return $json;
    }

    /**
     * cURL 28 "Operation timed out": la petición llegó y la API (1 worker + lock) pudo terminar igual.
     * cURL 28 "Connection timed out", 6 o 7: no se alcanzó el servicio.
     */
    private function connectionError(ConnectionException $e, float $start, ?string $localSha256): LarvasApiException
    {
        $message = $e->getMessage();
        $timedOutAfterConnecting = stripos($message, 'cURL error 28') !== false
            && stripos($message, 'Connection timed out') === false
            && stripos($message, 'Resolving timed out') === false;

        $exception = new LarvasApiException(
            $timedOutAfterConnecting ? 'api_timeout_possibly_completed' : 'api_unreachable',
            $message,
            null,
            null,
            $this->elapsedMs($start)
        );

        $this->log($exception, $localSha256);

        return $exception;
    }

    private function log(LarvasApiException $e, ?string $localSha256): void
    {
        $level = in_array($e->errorCode, ['api_unauthorized', 'hash_mismatch'], true) ? 'critical' : 'error';

        // image_sha256[:12] permite cruzar con el log de API_larvas (que registra el mismo prefijo)
        Log::log($level, "API_larvas: {$e->errorCode}", [
            'http_status' => $e->httpStatus,
            'image_sha256' => $localSha256 ? substr($localSha256, 0, 12) : null,
            'detail' => $e->getMessage(),
        ]);
    }

    private function elapsedMs(float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }
}
