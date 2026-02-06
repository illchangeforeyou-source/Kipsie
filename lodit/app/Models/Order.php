<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    'customer_name',
    'items',
    'total',
    'status',
    'user_id',
    'delivery_status',
    'delivery_notes',
    'shipped_at',
    'delivered_at',
];


    protected $casts = [
        'items' => 'array', // automatically decode JSON to array
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get notifications for this order
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
