<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Reglas de los 8 bioensayos (se registran en AuthServiceProvider).
 * Un bioensayo validado queda bloqueado: no se modifica ni se elimina hasta que lo reabran.
 */
class BioassayPolicy
{
    use HandlesAuthorization;

    public function validateBioassay(User $user, $bioassay)
    {
        return $user->hasRole(['Manager', 'Supervisor', 'Area Manager']);
    }

    // Guardar cambios: cualquiera que pueda entrar al bioensayo, mientras no esté validado
    public function update(User $user, $bioassay)
    {
        return $bioassay->isLocked()
            ? $this->deny('Este bioensayo ya fue validado. Un Supervisor o Jefe de Área debe reabrirlo para modificarlo.')
            : true;
    }

    // Reabrir un bioensayo validado (quita la validación)
    public function reopen(User $user, $bioassay)
    {
        return $bioassay->isLocked() && $user->hasAnyRole(User::ADMIN_ROLES);
    }

    // Eliminar (va a la papelera): solo Supervisor y Jefe de Área, y nunca uno validado
    public function delete(User $user, $bioassay)
    {
        if (! $user->hasAnyRole(User::ADMIN_ROLES)) {
            return $this->deny('Solo el Supervisor o el Jefe de Área pueden eliminar bioensayos.');
        }

        return $bioassay->isLocked()
            ? $this->deny('No se puede eliminar un bioensayo validado. Primero hay que reabrirlo.')
            : true;
    }
}
