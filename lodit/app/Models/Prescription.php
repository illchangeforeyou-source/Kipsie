<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'medicine_id',
        'pharmacist_id',
        'file_path',
        'status',
        'pharmacist_notes',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(User::class, 'pharmacist_id', 'id');
    }
}
