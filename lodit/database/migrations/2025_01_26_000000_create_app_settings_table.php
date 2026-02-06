<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->longText('setting_value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        \Illuminate\Support\Facades\DB::table('app_settings')->insert([
            ['setting_key' => 'app_name', 'setting_value' => 'KIPS', 'created_at' => now(), 'updated_at' => now()],
            ['setting_key' => 'app_logo_path', 'setting_value' => 'foto/logo.jpg', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
