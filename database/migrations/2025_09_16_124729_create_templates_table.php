<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');                // Título de plantilla
            $table->string('code')->nullable();     // Código interno
            $table->integer('version')->default(1);
            $table->string('validity')->nullable(); // Vigencia (texto o ENUM si quieres normalizar)
            $table->string('type');                 // Tipo de plantilla (recepcion, bioensayo, rechazo, etc.)
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
        Schema::dropIfExists('templates');
    }
}
