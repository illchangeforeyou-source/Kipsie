<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SafeDB
{
    /**
     * Safely query the login table, returning null if it doesn't exist
     */
    public static function safeLoginQuery($callback)
    {
        try {
            if (Schema::hasTable('login')) {
                return $callback();
            }
        } catch (\Exception $e) {
            // Log and return null
            \Illuminate\Support\Facades\Log::debug('Login table query error: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Safely update/insert into login table
     */
    public static function safeLoginUpdate($callback)
    {
        try {
            if (Schema::hasTable('login')) {
                return $callback();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::debug('Login table update error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Check if login table exists
     */
    public static function loginTableExists()
    {
        try {
            return Schema::hasTable('login');
        } catch (\Exception $e) {
            return false;
        }
    }
}
