<?php

namespace App\Services\LarvasApi;

use RuntimeException;

/**
 * Falla al llamar a API_larvas. El error_code queda registrado en image_analyses.
 */
class LarvasApiException extends RuntimeException
{
    // Código estable => mensaje para el usuario
    public const MESSAGES = [
        'api_unreachable'                => 'El servicio de análisis no está disponible. Intenta de nuevo en unos minutos.',
        'api_timeout_possibly_completed' => 'El servicio de análisis no respondió a tiempo. Es posible que haya terminado igual; vuelve a subir la foto.',
        'api_unauthorized'               => 'SIDEc no está autorizado en el servicio de análisis (revisar LARVAS_API_KEY).',
        'api_bad_request'                => 'El servicio de análisis rechazó el archivo: no es una imagen válida.',
        'api_payload_too_large'          => 'La imagen supera el tamaño máximo que acepta el servicio de análisis.',
        'api_unprocessable'              => 'El servicio de análisis no pudo procesar la imagen.',
        'api_internal_error'             => 'Error interno del servicio de análisis.',
        'api_model_not_loaded'           => 'El modelo de análisis no está cargado en el servicio.',
        'api_unexpected_status'          => 'El servicio de análisis respondió de forma inesperada.',
        'hash_mismatch'                  => 'Error de integridad: el servicio analizó una imagen distinta a la enviada.',
        'contract_mismatch'              => 'La respuesta del servicio de análisis no tiene el formato esperado.',
    ];

    /** Código HTTP de la API => error_code */
    public const STATUS_CODES = [
        400 => 'api_bad_request',
        401 => 'api_unauthorized',
        413 => 'api_payload_too_large',
        422 => 'api_unprocessable',
        500 => 'api_internal_error',
        503 => 'api_model_not_loaded',
    ];

    /** @var string */
    public $errorCode;

    /** @var int|null */
    public $httpStatus;

    /** @var string|null cuerpo de la respuesta, tal cual llegó */
    public $rawBody;

    /** @var float|null */
    public $roundTripMs;

    public function __construct(string $errorCode, ?string $detail = null, ?int $httpStatus = null, ?string $rawBody = null, ?float $roundTripMs = null)
    {
        $this->errorCode = $errorCode;
        $this->httpStatus = $httpStatus;
        $this->rawBody = $rawBody;
        $this->roundTripMs = $roundTripMs;

        parent::__construct($detail ?? self::MESSAGES[$errorCode] ?? $errorCode);
    }

    public static function fromStatus(int $status, ?string $rawBody, ?float $roundTripMs = null): self
    {
        $code = self::STATUS_CODES[$status] ?? 'api_unexpected_status';

        return new self($code, self::detailFrom($rawBody) ?? self::MESSAGES[$code], $status, $rawBody, $roundTripMs);
    }

    /** Mensaje para mostrar al usuario (sin detalles técnicos). */
    public function userMessage(): string
    {
        return self::MESSAGES[$this->errorCode] ?? self::MESSAGES['api_unexpected_status'];
    }

    // FastAPI responde {"detail": "texto"} o, en errores de validación, {"detail": [{...,"msg": "..."}]}
    private static function detailFrom(?string $rawBody): ?string
    {
        $json = json_decode((string) $rawBody, true);
        $detail = is_array($json) ? ($json['detail'] ?? null) : null;

        if (is_string($detail)) {
            return $detail;
        }

        if (is_array($detail)) {
            return collect($detail)->map(fn ($item) => is_array($item) ? implode('.', (array) ($item['loc'] ?? [])) . ': ' . ($item['msg'] ?? '') : (string) $item)
                ->implode('; ');
        }

        return null;
    }
}
