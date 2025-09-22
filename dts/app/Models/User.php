<?php

namespace App\Models;

use App\Modules\Authentication\Models\User as AuthenticationUser;

class User extends AuthenticationUser
{
   public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'roleID', 'roleID');
    }
}