<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $fillable = [
        'user_id',
        'permission_key',
        'can_access',
        'notes'
    ];

    protected $casts = [
        'can_access' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
