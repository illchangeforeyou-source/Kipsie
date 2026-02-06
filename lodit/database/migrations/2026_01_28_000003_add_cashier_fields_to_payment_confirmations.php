<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if columns already exist before adding them
        if (!Schema::hasColumn('payment_confirmations', 'confirmed_by')) {
            Schema::table('payment_confirmations', function (Blueprint $table) {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirmed_at');
            });
        }

        if (!Schema::hasColumn('payment_confirmations', 'rejected_by')) {
            Schema::table('payment_confirmations', function (Blueprint $table) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('confirmed_by');
            });
        }

        if (!Schema::hasColumn('payment_confirmations', 'rejected_reason')) {
            Schema::table('payment_confirmations', function (Blueprint $table) {
                $table->text('rejected_reason')->nullable()->after('rejected_by');
            });
        }

        if (!Schema::hasColumn('payment_confirmations', 'rejected_at')) {
            Schema::table('payment_confirmations', function (Blueprint $table) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_reason');
            });
        }

        // Add foreign keys if they don't exist
        try {
            Schema::table('payment_confirmations', function (Blueprint $table) {
                // Check if foreign key exists before adding
                $sm = DB::connection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableForeignKeys('payment_confirmations');
                $foreignKeyNames = array_map(function ($fk) { return $fk->getName(); }, $indexes);
                
                if (!in_array('payment_confirmations_confirmed_by_foreign', $foreignKeyNames)) {
                    $table->foreign('confirmed_by')->references('id')->on('login')->onDelete('set null');
                }
                
                if (!in_array('payment_confirmations_rejected_by_foreign', $foreignKeyNames)) {
                    $table->foreign('rejected_by')->references('id')->on('login')->onDelete('set null');
                }
            });
        } catch (\Exception $e) {
            // Foreign keys might already exist or fail silently
        }
    }

    public function down(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $table) {
            // Drop foreign keys if they exist
            try {
                $table->dropForeign(['confirmed_by']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropForeign(['rejected_by']);
            } catch (\Exception $e) {}
            
            // Drop columns if they exist
            $columns = ['confirmed_by', 'rejected_by', 'rejected_reason', 'rejected_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('payment_confirmations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
