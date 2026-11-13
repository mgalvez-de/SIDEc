<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Se quitó la Papelera: eliminar vuelve a borrar de verdad.
 * Solo hace algo en las bases que alcanzaron a crear la columna deleted_at (versión del 11-11).
 * Lo que estaba en la papelera se borra definitivamente antes de quitar la columna;
 * si no, reaparecería como si nunca se hubiera eliminado.
 */
class RemoveTrashFromRecords extends Migration
{
    // Primero los bioensayos y después las tablas de las que dependen
    private const TABLES = [
        'daphnia_magna_templates',
        'daphnia_magna_chronic',
        'isochrysis_galbana',
        'selenastrum_capricornutum',
        'tisbe_longicornis_water',
        'tisbe_longicornis_riles',
        'arbacia_fertilization',
        'arbacia_larval_stages',
        'sample_entries',
        'reception_templates',
        'rejection_templates',
    ];

    public function up()
    {
        foreach (self::TABLES as $tableName) {
            if (! Schema::hasColumn($tableName, 'deleted_at')) {
                continue;
            }

            DB::table($tableName)->whereNotNull('deleted_at')->delete();

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }

    public function down()
    {
        // La Papelera no se vuelve a crear
    }
}
