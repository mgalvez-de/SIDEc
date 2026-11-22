<?php

namespace App\Console\Commands;

use App\Models\Equipment;
use Illuminate\Console\Command;

/**
 * Registra un equipo del laboratorio desde la consola (todavía no hay pantalla para administrarlos).
 *
 *   php artisan equipment:register microscope EQ-MIC-01 "Microscopio triocular" --brand=Olympus --serial=123
 *   php artisan equipment:register camera EQ-CAM-01 "Cámara Basler" --brand=Basler --model=acA1300
 */
class RegisterEquipment extends Command
{
    protected $signature = 'equipment:register
                            {type : microscope | camera | stage_micrometer | other}
                            {code : Código interno único, ej. EQ-MIC-01}
                            {name : Nombre descriptivo}
                            {--brand=} {--model=} {--serial=} {--location=} {--notes=}';

    protected $description = 'Registra un equipo del laboratorio (microscopio, cámara, micrómetro...)';

    public function handle()
    {
        $type = $this->argument('type');
        $code = trim($this->argument('code'));

        if (! array_key_exists($type, Equipment::TYPES)) {
            $this->error('Tipo inválido. Usa uno de: ' . implode(', ', array_keys(Equipment::TYPES)));

            return 1;
        }

        if (Equipment::withTrashed()->where('code', $code)->exists()) {
            $this->error("Ya existe un equipo con el código {$code}.");

            return 1;
        }

        $equipment = Equipment::create([
            'type' => $type,
            'code' => $code,
            'name' => trim($this->argument('name')),
            'brand' => $this->option('brand'),
            'model' => $this->option('model'),
            'serial_number' => $this->option('serial'),
            'location' => $this->option('location'),
            'notes' => $this->option('notes'),
            'status' => 'active',
        ]);

        $this->info("Equipo registrado: #{$equipment->id} {$equipment->label()} (" . Equipment::TYPES[$type] . ')');

        return 0;
    }
}
