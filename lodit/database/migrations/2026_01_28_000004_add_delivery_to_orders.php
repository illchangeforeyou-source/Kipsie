<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('delivery_status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending')->after('status');
            $table->timestamp('shipped_at')->nullable()->after('delivery_status');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->text('delivery_notes')->nullable()->after('delivered_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_status', 'shipped_at', 'delivered_at', 'delivery_notes']);
        });
    }
};
