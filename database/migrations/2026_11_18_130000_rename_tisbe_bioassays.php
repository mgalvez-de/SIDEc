<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Usa en las recepciones ya guardadas el nombre oficial de los bioensayos de Tisbe
 * (según sus hojas de registro): "Tisbe longicornis", no "Tisbe biconicornis".
 */
class RenameTisbeBioassays extends Migration
{
    private const NAMES = [
        'Tisbe biconicornis Agua'      => 'Tisbe longicornis Aguas marinas',
        'Tisbe biconicornis Sedimento' => 'Tisbe longicornis Sustancias químicas/Riles',
    ];

    public function up()
    {
        $this->rename(self::NAMES);
    }

    public function down()
    {
        $this->rename(array_flip(self::NAMES));
    }

    private function rename(array $names): void
    {
        DB::table('reception_templates')->whereNotNull('assigned_bioassays')->orderBy('id')
            ->each(function ($reception) use ($names) {
                $bioassays = json_decode($reception->assigned_bioassays, true);

                if (!is_array($bioassays)) {
                    return;
                }

                $renamed = array_map(fn ($name) => $names[$name] ?? $name, $bioassays);

                if ($renamed !== $bioassays) {
                    DB::table('reception_templates')->where('id', $reception->id)
                        ->update(['assigned_bioassays' => json_encode($renamed)]);
                }
            });
    }
}
