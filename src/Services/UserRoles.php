<?php

namespace App\Services;

use App\Entity\User;

trait UserRoles
{
    public function isAdmin(User $user)
    {
        return in_array('ROLE_ADMIN', $user->getRoles()) ?? false;
    }
}