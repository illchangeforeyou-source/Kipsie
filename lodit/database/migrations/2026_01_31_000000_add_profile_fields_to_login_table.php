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
        Schema::table('login', function (Blueprint $table) {
            // Add profile_picture column if it doesn't exist
            if (!Schema::hasColumn('login', 'profile_picture')) {
                $table->text('profile_picture')->nullable()->after('password');
            }
            
            // Add email column if it doesn't exist
            if (!Schema::hasColumn('login', 'email')) {
                $table->string('email')->nullable()->unique()->after('username');
            }
            
            // Add name column if it doesn't exist
            if (!Schema::hasColumn('login', 'name')) {
                $table->string('name')->nullable()->after('username');
            }
            
            // Add phone column if it doesn't exist
            if (!Schema::hasColumn('login', 'phone')) {
                $table->string('phone')->nullable()->after('password');
            }
            
            // Add level column if it doesn't exist
            if (!Schema::hasColumn('login', 'level')) {
                $table->integer('level')->default(4)->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login', function (Blueprint $table) {
            if (Schema::hasColumn('login', 'profile_picture')) {
                $table->dropColumn('profile_picture');
            }
            if (Schema::hasColumn('login', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('login', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('login', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('login', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
