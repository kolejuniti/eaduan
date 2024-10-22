<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    // Specify the connection name
    protected $connection = 'emanagement';
    
    // Specify the table name if it's different from 'hostels'
    protected $table = 'tblstudent_hostel';

    // Define the primary key if it's not 'id'
    protected $primaryKey = 'student_ic';
}
