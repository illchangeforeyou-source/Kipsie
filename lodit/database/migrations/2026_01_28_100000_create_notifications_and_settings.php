<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notifications table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title');
                $table->text('message');
                $table->string('type')->default('info'); // info, success, warning, error, order
                $table->unsignedBigInteger('order_id')->nullable();
                $table->boolean('read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('login')->onDelete('cascade');
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            });
        }

        // App settings table
        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->string('setting_key')->unique();
                $table->text('setting_value')->nullable();
                $table->string('setting_type')->default('text'); // text, image, number, boolean
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // User permissions/accessibility table
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('permission_key');
                $table->boolean('can_access')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('login')->onDelete('cascade');
                $table->unique(['user_id', 'permission_key']);
            });
        }

        // User theme preference
        if (!Schema::hasTable('user_preferences')) {
            Schema::create('user_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('theme')->default('light'); // light or dark
                $table->boolean('notifications_enabled')->default(true);
                $table->string('notification_sound')->default('default');
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('login')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('notifications');
    }
};
