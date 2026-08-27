<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daphnia_magna_chronic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('templates')->onDelete('cascade');

            // === TEMPORIZADOR ===
            // El ensayo crónico tiene un tiempo límite de 21 días
            $table->string('timer_start')->nullable();

            // === DATOS GENERALES ===
            $table->string('sample');
            $table->string('matrix')->nullable();
            $table->datetime('bioassay_start')->nullable();
            $table->datetime('bioassay_end')->nullable();
            $table->string('analyst')->nullable();

            // === DATOS PRELIMINARES ===
            $table->decimal('sample_temperature', 5, 1)->nullable();
            $table->decimal('ph', 5, 2)->nullable();

            // === MANTENCIÓN DE ESPECIE (JSON) ===
            // Almacena datos de mantención cada 3 días (día 0, 3, 6, 9, 12, 15, 18):
            // - water_date: Fecha de agua reconstituida
            // - food_date: Fecha de alimento
            // - microalgae_ml: Cantidad de microalga en ml
            $table->json('maintenance_data')->nullable();

            // === CONTROL (JSON) ===
            // Almacena 21 días x 10 réplicas de conteo de juveniles
            // Incluye fila de suma por réplica
            $table->json('control_data')->nullable();
            $table->integer('control_total_reproduction')->nullable();

            // === CONCENTRACIONES (JSON) ===
            // Almacena hasta 5 concentraciones, cada una con:
            // - value: Valor de la concentración (ej: "100%", "50%", etc.)
            // - days: 21 días x 10 réplicas de conteo
            // - sum: Suma por réplica
            // - total_reproduction: Total de reproducción
            $table->json('concentrations_data')->nullable();

            // === RESULTADOS ===
            // NOEC: No Observed Effect Concentration
            // LOEC: Lowest Observed Effect Concentration
            $table->string('noec')->nullable();
            $table->string('loec')->nullable();
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daphnia_magna_chronic');
    }
};