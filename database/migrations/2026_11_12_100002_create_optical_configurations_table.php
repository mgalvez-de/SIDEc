<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración óptica = microscopio + objetivo + cámara. La calibración µm/px es de la
 * configuración completa, no de un equipo suelto (NCh-ISO/IEC 17025, 6.5).
 */
class CreateOpticalConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::create('optical_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                    // ej. OPT-MIC01-10X-CAM01
            $table->foreignId('microscope_id')->constrained('equipment');
            $table->foreignId('camera_id')->constrained('equipment');
            $table->string('objective_magnification', 20);       // ej. 10x
            $table->string('adapter')->nullable();               // adaptador C-mount u otro
            $table->string('status')->default('active');         // active | retired
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('optical_configurations');
    }
}
