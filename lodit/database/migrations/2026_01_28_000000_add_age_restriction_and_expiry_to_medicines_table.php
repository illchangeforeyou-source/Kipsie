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
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('age_restriction')->nullable()->comment('Age restriction for medicine (e.g., 18+, 21+)');
            $table->date('expiry_date')->nullable()->comment('Expiry date of the medicine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['age_restriction', 'expiry_date']);
        });
    }
};
