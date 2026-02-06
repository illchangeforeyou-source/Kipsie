<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            if (!Schema::hasColumn('medicines', 'requires_prescription')) {
                $table->boolean('requires_prescription')->default(false)->comment('If true, requires doctor prescription before purchase');
            }
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            if (Schema::hasColumn('medicines', 'requires_prescription')) {
                $table->dropColumn('requires_prescription');
            }
        });
    }
};
