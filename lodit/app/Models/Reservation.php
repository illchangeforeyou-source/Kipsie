<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations'; // optional if table name is plural

    protected $fillable = [
        'patient_name',
        'phone',
        'email',
        'doctor',
        'date',
        'status',
        'complaint',
    ];
}
