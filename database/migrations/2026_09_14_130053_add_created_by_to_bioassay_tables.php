<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToBioassayTables extends Migration
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
                $table->foreignId('created_by')->nullable()
                    ->constrained('users')->onDelete('set null');
            });
        }
    }
    
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
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
}
}