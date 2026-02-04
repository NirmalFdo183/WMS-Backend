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
        Schema::dropIfExists('loadings');
        Schema::create('loadings', function (Blueprint $table) {
            $table->id();
            $table->string('load_number');
            $table->foreignId('truck_id')->constrained('trucks')->cascadeOnDelete();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->date('prepared_date')->nullable();
            $table->date('loading_date')->nullable();
            $table->enum('status', ['pending', 'delivered', 'not_delivered'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loadings');
    }
};
