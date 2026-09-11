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
        Schema::create('batch__stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('supplier_invoice_id')->constrained('supplier_invoices');
            $table->integer('no_cases');
            $table->integer('pack_size');
            $table->integer('qty');
            $table->decimal('retail_price', 10, 2);
            $table->decimal('netprice', 10, 2);
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch__stocks');
    }
};
