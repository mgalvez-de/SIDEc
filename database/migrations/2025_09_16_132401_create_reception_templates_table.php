<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceptionTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reception_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');

            $table->string('thermometer_code')->nullable();     // Código del termómetro
            $table->string('correction_factor')->nullable();    // Factor de corrección
            $table->dateTime('received_at')->nullable();        // Fecha y hora de recepción
            $table->string('delivered_by')->nullable();         // Nombre de quien la entrega
            $table->string('client')->nullable();               // Cliente
            $table->dateTime('sampled_at')->nullable();         // Fecha y hora de muestreo
            $table->string('received_by')->nullable();          // Nombre de quién recibe
            $table->string('sample_identifier')->nullable();    // Identificación de la muestra
            $table->string('matrix')->nullable();               // Matriz
            $table->string('internal_sample_code')->nullable(); // Código interno de muestra
            $table->float('temperature_received')->nullable();
            $table->float('temperature_corrected')->nullable();
            $table->integer('report_number')->nullable();       // N° de Informe

            $table->json('assigned_bioassays')->nullable(); // Bioensayos asignados (array)

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
        Schema::dropIfExists('reception_templates');
    }
}
