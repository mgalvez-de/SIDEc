<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Limpieza del esquema heredado y relación real entre recepción e ingreso:
 *
 * 1. El código interno de la muestra pasa a ser único en recepciones y en ingresos.
 * 2. El ingreso queda unido a su recepción con una clave foránea sobre ese código:
 *    no puede existir un ingreso sin recepción, y no se puede eliminar una recepción ya ingresada.
 * 3. Se eliminan las tablas `bioassays` y `reception_template_bioassay`, que no se usan
 *    (los bioensayos solicitados se guardan en reception_templates.assigned_bioassays).
 * 4. Se eliminan columnas que ya no se usan: las de los cronómetros (reemplazados por la fecha
 *    y hora de inicio y de término) y `sample_entries.state` (el estado se calcula).
 */
class LimpiarEsquemaYUnirRecepcionConIngreso extends Migration
{
    // Columnas de los cronómetros eliminados, por tabla
    private const COLUMNAS_SIN_USO = [
        'daphnia_magna_templates'   => ['preliminary_timer_start', 'definitive_timer_start'],
        'daphnia_magna_chronic'     => ['timer_start'],
        'isochrysis_galbana'        => ['timer_start'],
        'selenastrum_capricornutum' => ['timer_start'],
        'tisbe_longicornis_water'   => ['timer_start'],
        'tisbe_longicornis_riles'   => ['preliminary_timer_start', 'definitive_timer_start'],
        'arbacia_fertilization'     => ['timer_start'],
        'arbacia_larval_stages'     => ['timer_start'],
        'sample_entries'            => ['state'],
    ];

    public function up()
    {
        Schema::dropIfExists('reception_template_bioassay');
        Schema::dropIfExists('bioassays');

        // SQLite (pruebas) no puede borrar columnas sin doctrine/dbal; ahí no hacen daño
        if (DB::getDriverName() !== 'sqlite') {
            foreach (self::COLUMNAS_SIN_USO as $tabla => $columnas) {
                $existentes = array_values(array_filter($columnas, fn ($c) => Schema::hasColumn($tabla, $c)));
                if ($existentes) {
                    Schema::table($tabla, fn (Blueprint $table) => $table->dropColumn($existentes));
                }
            }
        }

        Schema::table('reception_templates', function (Blueprint $table) {
            $table->unique('internal_sample_code');
        });

        Schema::table('sample_entries', function (Blueprint $table) {
            $table->unique('internal_sample_code');
            $table->foreign('internal_sample_code')
                ->references('internal_sample_code')->on('reception_templates')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('sample_entries', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['internal_sample_code']);
            }
            $table->dropUnique(['internal_sample_code']);
        });

        Schema::table('reception_templates', function (Blueprint $table) {
            $table->dropUnique(['internal_sample_code']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            foreach (self::COLUMNAS_SIN_USO as $tabla => $columnas) {
                Schema::table($tabla, function (Blueprint $table) use ($tabla, $columnas) {
                    foreach ($columnas as $columna) {
                        if (! Schema::hasColumn($tabla, $columna)) {
                            $table->string($columna)->nullable();
                        }
                    }
                });
            }
        }

        Schema::create('bioassays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('reception_template_bioassay', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reception_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('bioassay_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
}
