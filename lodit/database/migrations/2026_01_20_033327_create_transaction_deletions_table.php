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
        Schema::create('transaction_deletions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('deleted_by_user_id'); // User who deleted it
            $table->string('action')->default('soft_delete'); // soft_delete or permanent_delete
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->longText('transaction_data')->nullable(); // JSON backup of transaction
            $table->timestamp('deleted_at');
            $table->timestamps();

            // Foreign keys without cascade to avoid issues
            // $table->foreign('deleted_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_deletions');
    }
};
