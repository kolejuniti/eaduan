<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

class Student extends Authenticatable
{
    use Notifiable;

    protected $connection = 'eduhub';
    protected $table = 'students';

    protected $fillable = ['no_matric', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected static function boot()
    {
        parent::boot();

        // Add a global scope to limit results to students with status = 2
        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', 2);
        });
    }
}
