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
        Schema::dropIfExists('isochrysis_galbana');

        Schema::create('isochrysis_galbana', function (Blueprint $table) {
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
            $table->decimal('initial_inoculum_vol', 10, 2)->nullable();
            $table->date('stock_culture_date')->nullable();

            // === CRECIMIENTO Y PH (CONTROL) ===
            $table->decimal('rc24h', 10, 2)->nullable();
            $table->decimal('rc48h', 10, 2)->nullable();
            $table->decimal('rc72h', 10, 2)->nullable();
            $table->decimal('rc196h', 10, 2)->nullable();
            $table->decimal('rc296h', 10, 2)->nullable();
            $table->decimal('rc396h', 10, 2)->nullable();
            $table->decimal('rc496h', 10, 2)->nullable();
            $table->decimal('rc596h', 10, 2)->nullable();
            $table->decimal('rc696h', 10, 2)->nullable();
            $table->decimal('ph_initial', 8, 2)->nullable();
            $table->decimal('ph_final', 8, 2)->nullable();
            $table->decimal('growth_rate_control', 10, 4)->nullable();

            // === MEDICIONES (JSON) ===
            $table->json('measurements')->nullable();

            // === RESULTADOS ===
            $table->string('ec50_detail')->nullable();
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
        Schema::dropIfExists('isochrysis_galbana');
    }
};