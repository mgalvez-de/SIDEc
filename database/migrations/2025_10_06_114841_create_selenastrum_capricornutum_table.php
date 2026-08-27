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
        Schema::dropIfExists('selenastrum_capricornutum');

        Schema::create('selenastrum_capricornutum', function (Blueprint $table) {
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

            // === CRECIMIENTO Y PH (CONTROL) ===
            $table->decimal('rc24h', 10, 2)->nullable();
            $table->decimal('rc48h', 10, 2)->nullable();
            $table->decimal('rc72h', 10, 2)->nullable();
            $table->decimal('rc196h', 10, 2)->nullable();
            $table->decimal('rc296h', 10, 2)->nullable();
            $table->decimal('rc396h', 10, 2)->nullable();
            $table->decimal('rc496h', 10, 2)->nullable();
            $table->decimal('ph_initial', 8, 2)->nullable();
            $table->decimal('ph_final', 8, 2)->nullable();
            $table->decimal('control_growth_rate', 10, 4)->nullable();

            // === MEDICIONES (JSON) ===
            // Estructura: {
            //   "1": { "sample_or_concentration": "...", "ph_initial": ..., "ph_final": ..., 
            //          "r1_24h": ..., "r1_48h": ..., "r1_72h": ..., 
            //          "r2_24h": ..., "r2_48h": ..., "r2_72h": ..., 
            //          "r3_24h": ..., "r3_48h": ..., "r3_72h": ..., 
            //          "r4_24h": ..., "r4_48h": ..., "r4_72h": ..., 
            //          "r196h": ..., "r296h": ..., "r396h": ..., "r496h": ...,
            //          "growth_rate": ..., "percent_growth_rate": ..., "percent_inhibition": ..., "ce50": ... },
            //   "2": { ... },
            //   ...
            //   "5": { ... }
            // }
            $table->json('measurements')->nullable();

            // === RESULTADOS ===
            $table->string('ce50_detail')->nullable();
            $table->decimal('variation_coefficient', 8, 2)->nullable();
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selenastrum_capricornutum');
    }
};