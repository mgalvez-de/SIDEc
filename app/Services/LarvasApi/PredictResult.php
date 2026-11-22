<?php

namespace App\Services\LarvasApi;

/**
 * Respuesta válida de POST /api/v1/predict (contrato PredictResponse de API_larvas/app/schemas.py).
 * Se construye solo desde fromBody(), que valida el contrato y el hash de la imagen.
 */
class PredictResult
{
    /** @var array respuesta decodificada */
    public $data;

    /** @var string cuerpo tal cual llegó (para conservarlo y calcular su hash) */
    public $rawBody;

    /** @var float */
    public $roundTripMs;

    /** @var int */
    public $httpStatus;

    private function __construct(array $data, string $rawBody, float $roundTripMs, int $httpStatus)
    {
        $this->data = $data;
        $this->rawBody = $rawBody;
        $this->roundTripMs = $roundTripMs;
        $this->httpStatus = $httpStatus;
    }

    /**
     * @throws LarvasApiException contract_mismatch | hash_mismatch
     */
    public static function fromBody(string $rawBody, string $localSha256, float $roundTripMs, int $httpStatus = 200): self
    {
        $fail = fn (string $detail) => new LarvasApiException('contract_mismatch', $detail, $httpStatus, $rawBody, $roundTripMs);

        $data = json_decode($rawBody, true);
        if (! is_array($data)) {
            throw $fail('La respuesta no es JSON.');
        }

        foreach (['status', 'api_version', 'request_id', 'timestamp_utc', 'model_info', 'params',
                  'image_metadata', 'inference_time_ms', 'summary', 'instances'] as $key) {
            if (! array_key_exists($key, $data)) {
                throw $fail("Falta la clave \"$key\".");
            }
        }

        if ($data['status'] !== 'success') {
            throw $fail('status distinto de "success".');
        }

        foreach (['model_file', 'model_sha256', 'available_classes'] as $key) {
            if (! isset($data['model_info'][$key])) {
                throw $fail("Falta model_info.$key.");
            }
        }
        foreach (['width', 'height', 'image_sha256'] as $key) {
            if (! isset($data['image_metadata'][$key])) {
                throw $fail("Falta image_metadata.$key.");
            }
        }
        foreach (['total_instances', 'complete_instances', 'border_touching_instances', 'counts_by_class', 'complete_counts_by_class'] as $key) {
            if (! array_key_exists($key, $data['summary'])) {
                throw $fail("Falta summary.$key.");
            }
        }
        if (! is_array($data['instances']) || ! is_array($data['summary']['counts_by_class'])
            || ! is_array($data['summary']['complete_counts_by_class']) || ! is_array($data['model_info']['available_classes'])) {
            throw $fail('Tipos inválidos en instances, summary o model_info.');
        }

        $classes = array_values($data['model_info']['available_classes']);
        foreach ($data['instances'] as $instance) {
            foreach (['instance_id', 'class_id', 'label', 'confidence', 'touching_border', 'bbox_xyxy', 'morphometry'] as $key) {
                if (! is_array($instance) || ! array_key_exists($key, $instance)) {
                    throw $fail("Una instancia no tiene \"$key\".");
                }
            }
            if (! in_array($instance['label'], $classes, true)) {
                throw $fail("La etiqueta \"{$instance['label']}\" no está en available_classes.");
            }
        }

        $summary = $data['summary'];
        if (array_sum($summary['counts_by_class']) !== (int) $summary['total_instances']
            || count($data['instances']) !== (int) $summary['total_instances']
            || array_sum($summary['complete_counts_by_class']) !== (int) $summary['complete_instances']) {
            throw $fail('Los conteos de summary no cuadran con las instancias.');
        }

        if (! hash_equals(strtolower($localSha256), strtolower((string) $data['image_metadata']['image_sha256']))) {
            throw new LarvasApiException('hash_mismatch', null, $httpStatus, $rawBody, $roundTripMs);
        }

        return new self($data, $rawBody, $roundTripMs, $httpStatus);
    }

    /** Columnas de image_analyses que salen de la respuesta. */
    public function toAttributes(): array
    {
        $d = $this->data;

        return [
            'http_status'       => $this->httpStatus,
            'api_request_id'    => $d['request_id'],
            'api_version'       => $d['api_version'],
            'model_file'        => $d['model_info']['model_file'],
            'model_sha256'      => $d['model_info']['model_sha256'],
            'conf'              => $d['params']['conf'] ?? null,
            'iou'               => $d['params']['iou'] ?? null,
            'api_imgsz'         => $d['params']['imgsz'] ?? null,
            'api_timestamp_utc' => $d['timestamp_utc'],
            'inference_time_ms' => $d['inference_time_ms'],
            'round_trip_ms'     => round($this->roundTripMs, 1),
            'summary'           => $d['summary'],
            'instances'         => $d['instances'],
            'raw_response'      => $this->rawBody,
            'response_sha256'   => hash('sha256', $this->rawBody),
        ];
    }
}
