<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientToRejectionTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Para poder informarle a un cliente si alguna de sus muestras fue rechazada (Seguimiento por cliente).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rejection_templates', function (Blueprint $table) {
            $table->string('client')->nullable()->after('sample_identifier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rejection_templates', function (Blueprint $table) {
            $table->dropColumn('client');
        });
    }
}
