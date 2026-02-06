<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfirmation extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'cashier_id',
        'amount',
        'payment_method',
        'status',
        'notes',
        'confirmed_at',
        'confirmed_by',
        'rejected_by',
        'rejected_reason',
        'rejected_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
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

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }
}
