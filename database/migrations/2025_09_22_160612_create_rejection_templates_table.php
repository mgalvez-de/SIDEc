<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectionTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rejection_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->onDelete('cascade');

            $table->string('internal_sample_code')->nullable();     // Código interno de muestra
            $table->string('sample_identifier')->nullable();        // Identificación de la muestra
            $table->string('reason_for_rejection')->nullable();     // Motivo de rechazo
            $table->string('who_rejects')->nullable();              // Nombre de quién rechaza
            $table->string('who_informs_the_client')->nullable();   // Nombre de quién informa al cliente
            $table->string('customer_instructions')->nullable();    // Indicaciones del cliente
            $table->string('observations')->nullable();             // Observaciones

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
        Schema::dropIfExists('rejection_templates');
    }
}
