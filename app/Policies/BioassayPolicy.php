<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BioassayPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    public function update(User $user, $bioassay)
{
    return $user->id === $bioassay->created_by
        || $user->hasRole(['Manager', 'Area Manager']);
}

public function delete(User $user, $bioassay)
{
    return $user->id === $bioassay->created_by
        || $user->hasRole(['Manager', 'Area Manager']);
}

public function validateBioassay(User $user, $bioassay)
{
    return $user->hasRole(['Manager', 'Area Manager']);
}

}
