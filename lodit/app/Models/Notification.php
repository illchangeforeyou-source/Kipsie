<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable =   [
        'user_id',
        'title',
        'message',
        'type',
        'order_id',
        'read',
        'read_at'
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now()
        ]);
    }
}
