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
        Schema::table('batch__stocks', function (Blueprint $table) {
            $table->renameColumn('qty', 'remain_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch__stocks', function (Blueprint $table) {
            $table->renameColumn('remain_qty', 'qty');
        });
    }
};
