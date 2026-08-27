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
        Schema::dropIfExists('tisbe_longicornis_riles');

        Schema::create('tisbe_longicornis_riles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('templates')->onDelete('cascade');

            // === TEMPORIZADORES ===
            $table->string('preliminary_timer_start')->nullable();
            $table->string('definitive_timer_start')->nullable();

            // === DATOS GENERALES ===
            $table->string('sample');
            $table->string('matrix')->nullable();
            $table->string('analyst')->nullable();

            // === ENSAYO PRELIMINAR ===
            // Nota: Solo tiene Temperatura y Fecha de agua Control (sin pH)
            $table->datetime('preliminary_start_at')->nullable();
            $table->datetime('preliminary_end_at')->nullable();
            $table->decimal('preliminary_sample_temperature', 8, 2)->nullable();
            $table->date('preliminary_control_water_date')->nullable();
            $table->json('preliminary_table')->nullable();

            // === ENSAYO DEFINITIVO ===
            $table->datetime('definitive_start_at')->nullable();
            $table->datetime('definitive_end_at')->nullable();
            $table->decimal('definitive_sample_temperature', 8, 2)->nullable();
            $table->date('definitive_control_water_date')->nullable();
            $table->json('definitive_24h')->nullable();
            $table->json('definitive_48h')->nullable();

            // === RESULTADOS ===
            $table->string('control_immobility')->nullable();
            $table->string('cl50_24h')->nullable();
            $table->string('cl50_48h')->nullable();
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tisbe_longicornis_riles');
    }
};