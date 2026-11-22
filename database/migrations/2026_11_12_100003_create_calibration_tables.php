<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Calibraciones (NCh-ISO/IEC 17025, 6.4 y 6.5).
 * - equipment_calibrations: certificados de patrones (ej. micrómetro de platina).
 * - optical_calibrations: µm por píxel de una configuración óptica, medido con un patrón.
 * Solo se crea el esquema: todavía no hay pantalla para administrarlas.
 */
class CreateCalibrationTables extends Migration
{
    public function up()
    {
        Schema::create('equipment_calibrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->date('calibrated_at');
            $table->date('valid_until')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('provider')->nullable();
            $table->string('certificate_path')->nullable();     // archivo en el disco privado
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('optical_calibrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('optical_configuration_id')->constrained('optical_configurations');
            $table->decimal('um_per_px', 12, 6);
            $table->date('calibrated_at');
            $table->date('valid_until');
            $table->foreignId('reference_equipment_id')->nullable()->constrained('equipment'); // micrómetro usado
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('optical_calibrations');
        Schema::dropIfExists('equipment_calibrations');
    }
}
