<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Inventario de equipos del laboratorio (NCh-ISO/IEC 17025, 6.4): microscopios, cámaras,
 * micrómetros de platina, etc. Por ahora lo usan solo los análisis de imágenes de Arbacia.
 */
class CreateEquipmentTable extends Migration
{
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                   // Código interno, ej. EQ-MIC-01
            $table->string('name');                             // Nombre descriptivo
            $table->string('type')->index();                    // microscope | camera | stage_micrometer | other
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('active');        // active | out_of_service
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();                               // Un equipo dado de baja no se borra
        });
    }

    public function down()
    {
        Schema::dropIfExists('equipment');
    }
}
