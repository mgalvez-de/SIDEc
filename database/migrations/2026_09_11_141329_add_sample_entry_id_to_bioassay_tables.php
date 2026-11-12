<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSampleEntryIdToBioassayTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            'daphnia_magna_templates',
            'daphnia_magna_chronic',
            'isochrysis_galbana',
            'selenastrum_capricornutum',
            'tisbe_longicornis_water',
            'tisbe_longicornis_riles',
            'arbacia_fertilization',
            'arbacia_larval_stages',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('sample_entry_id')->nullable()
                    ->constrained('sample_entries')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'daphnia_magna_templates',
            'daphnia_magna_chronic',
            'isochrysis_galbana',
            'selenastrum_capricornutum',
            'tisbe_longicornis_water',
            'tisbe_longicornis_riles',
            'arbacia_fertilization',
            'arbacia_larval_stages',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['sample_entry_id']);
                $table->dropColumn('sample_entry_id');
            });
        }
    }
}
