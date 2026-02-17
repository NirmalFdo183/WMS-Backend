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
            $table->foreignId('sales_rep_id')->nullable()->constrained('sales_reps')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loadings', function (Blueprint $table) {
            $table->dropForeign(['sales_rep_id']);
            $table->dropColumn('sales_rep_id');
        });
    }
};
