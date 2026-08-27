<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSampleEntriesTableAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('sample_entries', function (Blueprint $table) {
        if (! Schema::hasColumn('sample_entries', 'template_id')) {
            $table->foreignId('template_id')->after('id')->constrained()->onDelete('cascade');
        }

        // revisa si las columnas existen en la base de datos, y si no, las agrega
        if (! Schema::hasColumn('sample_entries', 'received_at')) {
            $table->dateTime('received_at')->nullable()->after('template_id');
        }
        if (! Schema::hasColumn('sample_entries', 'internal_sample_code')) {
            $table->string('internal_sample_code')->nullable()->after('received_at');
        }
        if (! Schema::hasColumn('sample_entries', 'sample_type')) {
            $table->string('sample_type')->nullable()->after('internal_sample_code');
        }
        if (! Schema::hasColumn('sample_entries', 'sample_concentration')) {
            $table->decimal('sample_concentration', 8, 2)->nullable()->after('sample_type');
        }
        if (! Schema::hasColumn('sample_entries', 'parameter_reading_date')) {
            $table->date('parameter_reading_date')->nullable()->after('sample_concentration');
        }
        if (! Schema::hasColumn('sample_entries', 'analyst')) {
            $table->string('analyst')->nullable()->after('parameter_reading_date');
        }
        if (! Schema::hasColumn('sample_entries', 'ph')) {
            $table->decimal('ph', 8, 2)->nullable()->after('analyst');
        }
        if (! Schema::hasColumn('sample_entries', 'salinity')) {
            $table->decimal('salinity', 8, 2)->nullable()->after('ph');
        }
        if (! Schema::hasColumn('sample_entries', 'conductivity')) {
            $table->decimal('conductivity', 8, 2)->nullable()->after('salinity');
        }
        if (! Schema::hasColumn('sample_entries', 'dissolved_oxygen')) {
            $table->decimal('dissolved_oxygen', 8, 2)->nullable()->after('conductivity');
        }
        if (! Schema::hasColumn('sample_entries', 'temperature')) {
            $table->decimal('temperature', 8, 2)->nullable()->after('dissolved_oxygen');
        }
        if (! Schema::hasColumn('sample_entries', 'observations')) {
            $table->text('observations')->nullable()->after('temperature');
        }
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sample_entries', function (Blueprint $table) {
            //
        });
    }
}
