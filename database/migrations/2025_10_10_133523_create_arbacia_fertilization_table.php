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
        Schema::dropIfExists('arbacia_fertilization');

        Schema::create('arbacia_fertilization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('templates')->onDelete('cascade');

            // === TEMPORIZADOR ===
            // El ensayo de fecundación tiene un tiempo límite de 60 minutos
            $table->string('timer_start')->nullable();

            // === DATOS GENERALES ===
            // Solo una muestra y matriz por bioensayo
            $table->string('sample');
            $table->string('matrix')->nullable();
            $table->datetime('bioassay_start')->nullable();
            $table->string('analyst')->nullable();
            $table->decimal('control_fertilization_percentage', 8, 2)->nullable();

            // === TIEMPOS DEL ENSAYO ===
            $table->string('sperm_addition_time')->nullable(); // HH:MM
            $table->string('egg_addition_time')->nullable();   // HH:MM
            $table->string('fixation_time_end')->nullable();   // HH:MM
            $table->datetime('count_end_datetime')->nullable();

            // === FILAS DE DATOS (JSON) ===
            // Almacena hasta 15 filas con:
            // - concentration: Concentración o identificador
            // - r1_nf, r1_total, r1_fert: Réplica 1 (No Fecundados / Total / % Fecundación)
            // - r2_nf, r2_total, r2_fert: Réplica 2
            // - r3_nf, r3_total, r3_fert: Réplica 3
            // - avg_fertilization: Promedio de % fecundación
            // - inhibition: % de inhibición respecto al control
            // - ci: Concentración de inhibición
            $table->json('rows_data')->nullable();

            // === RESULTADOS ===
            $table->string('ci50')->nullable();
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arbacia_fertilization');
    }
};