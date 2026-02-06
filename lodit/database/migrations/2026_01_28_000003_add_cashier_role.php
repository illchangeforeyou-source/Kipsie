<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add cashier role (level 5) to level table if it exists
        if (Schema::hasTable('level')) {
            DB::table('level')->insertOrIgnore([
                'lvlnumber' => 5,
                'beingas' => 'Cashier',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('level')->where('lvlnumber', 5)->delete();
    }
};
