<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'notifications_enabled',
        'notification_sound'
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
