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
        Schema::dropIfExists('arbacia_larval_stages');

        Schema::create('arbacia_larval_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('templates')->onDelete('cascade');

            // === TEMPORIZADOR ===
            // El ensayo de estado larval tiene un tiempo límite de 48 horas
            $table->string('timer_start')->nullable();

            // === DATOS GENERALES ===
            // Solo una muestra y matriz por bioensayo
            $table->string('sample');
            $table->string('matrix')->nullable();
            $table->datetime('bioassay_start')->nullable();
            $table->string('analyst')->nullable();

            // === TIEMPOS DEL ENSAYO ===
            $table->string('fertilization_time')->nullable();        // HH:MM - Hora de fecundación
            $table->string('fertilized_eggs_added_at')->nullable();  // HH:MM - Hora adición óvulos fecundados
            $table->string('fixation_time_end')->nullable();         // HH:MM - Hora término fijación
            $table->datetime('count_end_datetime')->nullable();      // Fecha/hora término conteo

            // === CONTROL (JSON) ===
            // Almacena datos del control con 4 réplicas:
            // - r1_larva, r1_total, r1_percent: Réplica 1
            // - r2_larva, r2_total, r2_percent: Réplica 2
            // - r3_larva, r3_total, r3_percent: Réplica 3
            // - r4_larva, r4_total, r4_percent: Réplica 4
            // - larval_pluteus_avg: Promedio % larva pluteus
            // - total_larva_percent: % total de larvas
            $table->json('control_data')->nullable();

            // === FILAS DE DATOS (JSON) ===
            // Almacena hasta 15 filas con:
            // - concentration: Concentración
            // - r1_larva, r1_total, r1_percent: Réplica 1
            // - r2_larva, r2_total, r2_percent: Réplica 2
            // - r3_larva, r3_total, r3_percent: Réplica 3
            // - r4_larva, r4_total, r4_percent: Réplica 4
            // - larval_pluteus_percent: % larva pluteus promedio
            // - inhibition_percent: % de inhibición respecto al control
            // - ce: Concentración efectiva
            $table->json('rows_data')->nullable();

            // === RESULTADOS ===
            $table->string('ce50')->nullable();
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arbacia_larval_stages');
    }
};