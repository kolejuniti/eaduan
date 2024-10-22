<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;

    protected $connection = 'eduhub';
    protected $table = 'students';

    protected $fillable = ['no_matric', 'password'];

    protected $hidden = ['password', 'remember_token'];
}
