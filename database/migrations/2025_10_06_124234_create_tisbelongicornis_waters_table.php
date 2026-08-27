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
        Schema::dropIfExists('tisbe_longicornis_water');

        Schema::create('tisbe_longicornis_water', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('templates')->onDelete('cascade');

            // === TEMPORIZADOR ===
            $table->string('timer_start')->nullable();

            // === DATOS GENERALES ===
            $table->string('sample');
            $table->string('matrix')->nullable();
            $table->datetime('bioassay_start')->nullable();
            $table->datetime('bioassay_end')->nullable();
            $table->string('analyst')->nullable();

            // === DATOS PRELIMINARES ===
            $table->decimal('initial_inoculum', 10, 2)->nullable();
            $table->date('stock_culture_date')->nullable();

            // === DATOS DE MUESTRAS ===
            // JSON con estructura:
            // {
            //   "1": { "concentration": "...", "h24_r1": 0, "h24_r2": 0, ..., "sum_24h": 0, "h48_r1": 0, ..., "sum_48h": 0, "observations": "..." },
            //   "2": { ... },
            //   ...
            //   "24": { ... }
            // }
            $table->json('samples_data')->nullable();

            // === RESULTADOS ===
            $table->string('cl50_24h')->nullable();
            $table->string('cl50_48h')->nullable();
            $table->text('observations')->nullable();
            $table->string('vb')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tisbe_longicornis_water');
    }
};