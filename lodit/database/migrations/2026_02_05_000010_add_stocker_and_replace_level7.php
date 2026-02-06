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
            // Add Stocker as a new role (pick lvlnumber 8 if free)
            $exists8 = DB::table('level')->where('lvlnumber', 8)->first();
            if (!$exists8) {
                DB::table('level')->insertOrIgnore([
                    'lvlnumber' => 8,
                    'beingas' => 'Stocker',
                ]);
            }

            // Replace duplicate Pharmacist entry at level 7 with Cashier Leader
            $lvl7 = DB::table('level')->where('lvlnumber', 7)->first();
            if ($lvl7) {
                DB::table('level')->where('lvlnumber', 7)->update(['beingas' => 'Cashier Leader']);
            } else {
                // If level 7 missing, insert it as Cashier Leader
                DB::table('level')->insertOrIgnore([
                    'lvlnumber' => 7,
                    'beingas' => 'Cashier Leader',
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('level')) {
            // Remove Stocker (lvlnumber 8)
            DB::table('level')->where('lvlnumber', 8)->delete();

            // Try to restore level 7 label back to Pharmacist
            DB::table('level')->where('lvlnumber', 7)->update(['beingas' => 'Pharmacist']);
        }
    }
};
