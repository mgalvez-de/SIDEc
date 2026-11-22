<?php

namespace App\Models\Concerns;

use App\Models\User;

/**
 * Común a los 8 modelos de bioensayo:
 * - Trazabilidad: guarda quién lo modificó por última vez (updated_by).
 * - Bloqueo: un bioensayo validado no se modifica hasta que un Supervisor o Jefe de Área lo reabra.
 */
trait BioassayRecord
{
    protected static function bootBioassayRecord(): void
    {
        static::updating(function ($bioassay) {
            // Validar o reabrir no cuenta como "modificar los datos del ensayo"
            $dataChanged = collect($bioassay->getDirty())->except(['validated_by', 'validated_at'])->isNotEmpty();

            if ($dataChanged && auth()->check()) {
                $bioassay->updated_by = auth()->id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isLocked(): bool
    {
        return $this->validated_at !== null;
    }
}
