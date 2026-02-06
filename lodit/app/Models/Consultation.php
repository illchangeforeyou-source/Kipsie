<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'user_id',
        'consultant_id',
        'question',
        'response',
        'status',
        'medicine_id',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id', 'id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
