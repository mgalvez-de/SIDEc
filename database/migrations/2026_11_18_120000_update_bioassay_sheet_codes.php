<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Corrige el título, código, versión y vigencia de los bioensayos ya creados,
 * que se guardaron con datos provisorios (por ejemplo RT-01.XX).
 * Los datos correctos son los de las hojas oficiales (config/bioensayos.php).
 */
class UpdateBioassaySheetCodes extends Migration
{
    public function up()
    {
        foreach (config('bioensayos') as $sheet) {
            $table = (new $sheet['model'])->getTable();

            if (!Schema::hasTable($table)) {
                continue;
            }

            $templateIds = DB::table($table)->whereNotNull('template_id')->pluck('template_id');

            DB::table('templates')->whereIn('id', $templateIds)->update([
                'title'    => $sheet['title'],
                'code'     => $sheet['code'],
                'version'  => $sheet['version'],
                'validity' => $sheet['validity'],
            ]);
        }
    }

    public function down()
    {
        // Los datos anteriores eran provisorios: no hay nada que restaurar.
    }
}
