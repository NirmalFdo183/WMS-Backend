<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the columns the dashboard filters and sorts on, so the
 * database can jump straight to the rows instead of scanning whole tables.
 * Adding an index does not change any data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loadings', function (Blueprint $table) {
            $table->index('created_at', 'loadings_created_at_index');
            $table->index('status', 'loadings_status_index');
            $table->index('loading_date', 'loadings_loading_date_index');
        });

        Schema::table('batch__stocks', function (Blueprint $table) {
            $table->index('expiry_date', 'batch__stocks_expiry_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('loadings', function (Blueprint $table) {
            $table->dropIndex('loadings_created_at_index');
            $table->dropIndex('loadings_status_index');
            $table->dropIndex('loadings_loading_date_index');
        });

        Schema::table('batch__stocks', function (Blueprint $table) {
            $table->dropIndex('batch__stocks_expiry_date_index');
        });
    }
};
