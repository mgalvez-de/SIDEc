<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un registro por fotografía analizada con API_larvas (bioensayos de Arbacia spatuligera).
 *
 * Se inserta UNA sola vez, ya con la respuesta completa (o con el error). Después solo pueden
 * cambiar status, superseded_by_id, void_reason, voided_by, voided_at y updated_at; nada se borra.
 * (NCh-ISO/IEC 17025, 7.5 y 7.11). Reemplazar deja la foto anterior en "superseded" con
 * superseded_by_id; anular la deja en "superseded" sin reemplazo. En ambos casos se guardan el
 * motivo y quién y cuándo lo hizo.
 */
class CreateImageAnalysesTable extends Migration
{
    public function up()
    {
        Schema::create('image_analyses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();                       // Identificador único del análisis

            // === A QUÉ PERTENECE ===
            $table->morphs('analyzable');                         // ArbaciaLarvalStage | ArbaciaFertilization
            $table->foreignId('sample_entry_id')->nullable()->constrained('sample_entries');
            $table->string('row_key', 10);                        // "control" o "1".."15"
            $table->unsignedTinyInteger('replica');
            $table->unsignedSmallInteger('photo_number');         // Correlativo dentro de la réplica (sin máximo)
            $table->index(['analyzable_type', 'analyzable_id', 'row_key', 'replica'], 'image_analyses_replica_index');

            // === PARÁMETROS ===
            $table->decimal('conf', 4, 3)->nullable();            // Tomados de "params" de la respuesta
            $table->decimal('iou', 4, 3)->nullable();
            $table->unsignedSmallInteger('api_imgsz')->nullable();
            $table->boolean('requested_um')->default(false);

            // === EQUIPOS ===
            $table->foreignId('microscope_id')->constrained('equipment');
            $table->foreignId('camera_id')->constrained('equipment');
            $table->string('magnification', 20);
            $table->foreignId('optical_configuration_id')->nullable()->constrained('optical_configurations');
            $table->foreignId('optical_calibration_id')->nullable()->constrained('optical_calibrations');
            $table->decimal('um_per_px', 12, 6)->nullable();

            // === IMAGEN ORIGINAL Y CAPTURA ===
            $table->string('fixative', 30);
            $table->dateTime('captured_at')->nullable();
            $table->string('image_path');
            $table->char('image_sha256', 64);
            $table->string('image_original_name');
            $table->string('image_mime', 100);
            $table->unsignedBigInteger('image_size_bytes');
            $table->unsignedInteger('image_width')->nullable();
            $table->unsignedInteger('image_height')->nullable();

            // === RESPUESTA DE LA API ===
            $table->string('api_request_id', 64)->nullable();
            $table->string('api_version', 20)->nullable();
            $table->string('model_file')->nullable();
            $table->string('model_sha256', 64)->nullable();
            $table->string('api_timestamp_utc', 40)->nullable();
            $table->decimal('inference_time_ms', 10, 2)->nullable();
            $table->decimal('round_trip_ms', 10, 1)->nullable();
            $table->json('summary')->nullable();
            $table->json('instances')->nullable();               // Con polygon_xy y morfometría
            $table->longText('raw_response')->nullable();        // Bytes exactos (no json: MySQL lo reordenaría)
            $table->char('response_sha256', 64)->nullable();

            // === ESTADO ===
            $table->string('status', 20)->index();                // completed | failed | superseded
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('error_code', 50)->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('superseded_by_id')->nullable()->constrained('image_analyses');
            $table->text('void_reason')->nullable();             // Motivo del reemplazo o de la anulación
            $table->foreignId('voided_by')->nullable()->constrained('users'); // Quién la anuló o reemplazó
            $table->dateTime('voided_at')->nullable();           // Cuándo (17025, 7.5)
            $table->foreignId('performed_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('image_analyses');
    }
}
