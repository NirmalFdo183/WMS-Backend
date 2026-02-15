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
            $table->integer('extra_units')->default(0)->after('pack_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch__stocks', function (Blueprint $table) {
            $table->dropColumn('extra_units');
        });
    }
};
