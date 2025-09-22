<?php

namespace App\Modules\Authentication\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'userID';

    protected $fillable = [
        'username',
        'email',
        'password',
        'firstName',
        'middleName',
        'lastName',
        'roleID',
        'departmentID',
        'phoneNo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'roleID', 'roleID');
    }
}