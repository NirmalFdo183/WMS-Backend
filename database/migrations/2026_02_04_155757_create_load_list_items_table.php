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
       
        Schema::create('load_list_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('loading_id')->constrained('loadings')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('batch__stocks')->cascadeOnDelete();
            $table->integer('qty');
            $table->integer('free_qty')->nullable();
            $table->double('wh_price')->nullable();
            $table->double('net_price')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_list_items');
    }
};
