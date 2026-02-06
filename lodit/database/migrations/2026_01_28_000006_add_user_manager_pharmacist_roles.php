<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('level')) {
            // Add User Manager role (level 6)
            DB::table('level')->insertOrIgnore([
                'lvlnumber' => 6,
                'beingas' => 'User Manager',
            ]);

            // Add Pharmacist role (level 7)
            DB::table('level')->insertOrIgnore([
                'lvlnumber' => 7,
                'beingas' => 'Pharmacist',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('level')->whereIn('lvlnumber', [6, 7])->delete();
    }
};
