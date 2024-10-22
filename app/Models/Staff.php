<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable;

    protected $connection = 'eduhub';
    protected $table = 'users';

    protected $fillable = ['email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Hash the student's password.
     */
    // public function setPasswordAttribute($password)
    // {
    //     $this->attributes['password'] = bcrypt($password);
    // }
}
