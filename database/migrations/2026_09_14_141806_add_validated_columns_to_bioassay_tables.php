<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidatedColumnsToBioassayTables extends Migration
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
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('validated_by')->nullable()
                    ->constrained('users')->onDelete('set null');
                $table->timestamp('validated_at')->nullable();
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
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['validated_by']);
                $table->dropColumn('validated_by');
                $table->dropColumn('validated_at');
            });
        }
    }
}
