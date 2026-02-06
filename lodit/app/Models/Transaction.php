<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'category',
        'description',
        'amount',
        'date',
        'medicine_id',
        'quantity',
        'balance',
        'reference_id',
    ];
}
