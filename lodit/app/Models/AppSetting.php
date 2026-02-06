<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'app_settings';
    
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description'
    ];

    public static function get($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function set($key, $value, $type = 'text', $description = null)
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => $value,
                'setting_type' => $type,
                'description' => $description
            ]
        );
    }
}
