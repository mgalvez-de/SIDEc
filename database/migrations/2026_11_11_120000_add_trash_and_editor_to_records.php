<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Papelera y trazabilidad:
 * - deleted_at: lo eliminado queda oculto (no se borra) y se puede restaurar.
 * - updated_by: quién modificó por última vez cada bioensayo.
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

    private const OTHER_TABLES = ['sample_entries', 'reception_templates', 'rejection_templates'];

    public function up()
    {
        foreach (self::BIOASSAY_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->softDeletes();
            });
        }

        foreach (self::OTHER_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        foreach (self::BIOASSAY_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('updated_by');
                $table->dropSoftDeletes();
            });
        }

        foreach (self::OTHER_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
}
