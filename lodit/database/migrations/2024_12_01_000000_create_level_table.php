<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create level table with all roles
        if (!Schema::hasTable('level')) {
            Schema::create('level', function (Blueprint $table) {
                $table->id('lvlnumber');
                $table->string('beingas')->unique();
            });

            // Insert default roles
            DB::table('level')->insert([
                ['lvlnumber' => 1, 'beingas' => 'Super Admin'],
                ['lvlnumber' => 2, 'beingas' => 'Admin'],
                ['lvlnumber' => 3, 'beingas' => 'Doctor'],
                ['lvlnumber' => 4, 'beingas' => 'User'],
                ['lvlnumber' => 5, 'beingas' => 'Cashier'],
                ['lvlnumber' => 6, 'beingas' => 'User Manager'],
                ['lvlnumber' => 7, 'beingas' => 'Pharmacist'],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('level');
    }
};
