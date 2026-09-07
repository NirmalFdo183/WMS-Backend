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
        Schema::table('loadings', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('helper_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('cash_collector_id')->nullable()->constrained('employees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loadings', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['helper_id']);
            $table->dropForeign(['cash_collector_id']);
            $table->dropColumn(['driver_id', 'helper_id', 'cash_collector_id']);
        });
    }
};
