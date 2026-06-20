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
        Schema::create('loading_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loading_id')->constrained('loadings')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('batch__stocks')->onDelete('cascade');
            $table->integer('qty');
            $table->date('return_date');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loading_returns');
    }
};
