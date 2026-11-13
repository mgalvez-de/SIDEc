<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trazabilidad: updated_by guarda quién modificó por última vez cada bioensayo.
 *
 * (Esta migración también agregaba la Papelera, que se quitó el 13-11. Las bases que alcanzaron
 * a correr esa versión la limpian con 2026_11_13_120000_remove_trash_from_records.)
 */
class AddTrashAndEditorToRecords extends Migration
{
    private const BIOASSAY_TABLES = [
        'daphnia_magna_templates',
        'daphnia_magna_chronic',
        'isochrysis_galbana',
        'selenastrum_capricornutum',
        'tisbe_longicornis_water',
        'tisbe_longicornis_riles',
        'arbacia_fertilization',
        'arbacia_larval_stages',
    ];

    public function up()
    {
        foreach (self::BIOASSAY_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        foreach (self::BIOASSAY_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('updated_by');
            });
        }
    }
}
