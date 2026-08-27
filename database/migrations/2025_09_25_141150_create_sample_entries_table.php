<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSampleEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');

            $table->dateTime('received_at')->nullable();             // Fecha y hora de recepción
            $table->string('internal_sample_code')->nullable();      // Código interno de muestra
            $table->string('sample_type')->nullable();               // Tipo de muestra
            $table->decimal('sample_concentration')->nullable();     // Concentración de muestra
            $table->date('parameter_reading_date')->nullable();      // Fecha de lectura de parámetros
            $table->string('analyst')->nullable();                   // Analista

            $table->decimal('ph')->nullable();                       // pH
            $table->decimal('salinity')->nullable();                 // Salinidad
            $table->decimal('conductivity')->nullable();             // Conductividad
            $table->decimal('dissolved_oxygen')->nullable();         // Oxígeno disuelto
            $table->decimal('temperature')->nullable();              // Temperatura
            $table->string('observations')->nullable();              // Observaciones
            $table->string('state')->nullable();                     // Estado

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sample_entries');
    }
}
