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
        Schema::create('pending_admin_changes', function (Blueprint $table) {
            $table->id();
            $table->string('action_type'); // CREATE, UPDATE, DELETE, PASSWORD_CHANGE
            $table->unsignedBigInteger('target_user_id'); // User being affected
            $table->unsignedBigInteger('admin_id'); // Admin who made the change
            $table->json('old_data')->nullable(); // Previous values
            $table->json('new_data')->nullable(); // New values
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->unsignedBigInteger('approved_by')->nullable(); // Super admin who approved
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_admin_changes');
    }
};
